use crate::*;
use crate::types::Session;
use crate::filters::with_clients;

use futures::{FutureExt, StreamExt, stream::SplitStream};
use tokio_stream::wrappers::UnboundedReceiverStream;
use warp::filters::ws::{Ws, WebSocket};
use warp::{Filter, Reply, Rejection};
use tokio::sync::mpsc;

pub fn root(
    clients: &'static Clients
) -> impl Filter<Extract = (impl Reply,), Error = Rejection> + Clone {
    let root = warp::path("ws");

    let notifs = root
        .and(warp::path("notifs"))
        .and(warp::ws())
        .and(warp::cookie::<String>("auth_token"))
        .and(warp::path::param::<i32>())
        .and(warp::path::end())
        .and(with_clients(clients))
        .and_then(recv);

    notifs
}

async fn recv(
    ws: Ws,
    jwt: String,
    uid: i32,
    clients: &'static Clients
) -> Result<impl warp::Reply, warp::Rejection> {
    info!("Recieve notification endpoint reached");

    Ok(ws.on_upgrade( async move |socket| {
        info!("Upgrading");
        let stream = connect_client(socket, jwt.clone(), uid, clients);
        recv_thread(stream).await;
        destroy_client(uid, jwt, clients);
    }))
}

fn connect_client(
    socket: WebSocket,
    jwt: String,
    uid: i32,
    clients: &'static Clients
) -> SplitStream<WebSocket> {
    // Get Unbounded Streams and join them with sockets
    let (sink, stream) = socket.split();

    let (sender, receiver) = mpsc::unbounded_channel();

    let receiver = UnboundedReceiverStream::new(receiver);
    tokio::task::spawn(receiver.forward(sink).map(|result| {
        if let Err(e) = result {
            error!("Error sending websocket message: {}", e);
        }
    }));

    // Create and store user session
    let session = Session { jwt, sender };

    let mut locked = clients.lock().unwrap();
    let user_opt = locked.get_mut(&uid);

    match user_opt {
        Some(vec) => vec.push(session),
        None => { locked.insert(uid, vec![session]); },
    }

    // Return stream for continuous use in the 
    stream
}

async fn recv_thread(mut recv: SplitStream<WebSocket>) {
    while let Some(body) = recv.next().await {
        if let Ok(content) = body {
            info!("Got: {:?}", content);
        }
    }

    info!("client disconnected");
}

fn destroy_client(uid: i32, jwt: String, clients: &'static Clients) {
    let mut locked = clients.lock().unwrap();
    let user_opt = locked.get_mut(&uid);

    if let Some(v) = user_opt {
        v.retain(|session| session.jwt != jwt);
        if v.len() == 0 {
            locked.remove(&uid);
        }
    } else {
        error!("Destroyed client doesn't have related user sessions");
    }
}