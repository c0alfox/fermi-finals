use crate::*;

use warp::filters::ws::{WebSocket, Ws};
use futures::StreamExt;
use tokio::sync::mpsc;
use tokio_stream::wrappers::UnboundedReceiverStream;
use futures::FutureExt;

pub async fn register(
    uid: i32,
    jwt: String,
    clients: &'static Clients
) -> Result<impl warp::Reply, warp::Rejection> {
    info!("Registering client with uid {}", uid);
    clients.lock().unwrap().insert(jwt, Client { uid, sender: None });
    info!("User registered successfully");
    info!("All clients are {:?}", clients);

    Ok("User registered succcessfully")
}

pub async fn recv(
    ws: Ws,
    jwt: String,
    clients: &'static Clients
) -> Result<impl warp::Reply, warp::Rejection> {
    info!("Recieve notification endpoint reached");

    let locked = clients.lock().unwrap();
    let client = locked.get(&jwt).cloned();
    match client {
        Some(c) => Ok(ws.on_upgrade(
            move |socket| client_connection(socket, jwt, clients, c)
        )),
        None => Err(warp::reject::not_found()),
    }
}

pub async fn client_connection(
    ws: WebSocket,
    jwt: String,
    clients: &'static Clients,
    mut client: Client
) {
    let (client_ws_sender, mut client_ws_rcv) = ws.split();
    let (client_sender, client_rcv) = mpsc::unbounded_channel();

    let client_rcv = UnboundedReceiverStream::new(client_rcv);
    tokio::task::spawn(client_rcv.forward(client_ws_sender).map(|result| {
        if let Err(e) = result {
            eprintln!("error sending websocket msg: {}", e);
        }
    }));

    client.sender = Some(client_sender);
    clients.lock().unwrap().insert(jwt.clone(), client.clone());

    println!("{:?} connected", client);

    while let Some(result) = client_ws_rcv.next().await {
        let msg = match result {
            Ok(msg) => msg,
            Err(e) => {
                error!("error receiving ws message for id: {:?}): {}", client, e);
                break;
            }
        };
        info!("Received {}", msg.to_str().unwrap());
    }

    clients.lock().unwrap().remove(&jwt);
    println!("{:?} disconnected", client);
}
