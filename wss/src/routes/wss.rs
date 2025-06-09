use crate::*;

use warp::filters::ws::Ws;
use futures::{SinkExt, StreamExt};

pub async fn register(
    uid: i32,
    jwt: String,
    clients: &'static Clients
) -> Result<impl warp::Reply, warp::Rejection> {
    info!("Registering client with uid {}", uid);
    // 
    info!("User registered successfully");
    info!("All clients are {:?}", clients);

    Ok("User registered succcessfully")
}

pub async fn recv(
    ws: Ws,
    jwt: String,
    uid: i32,
    clients: &'static Clients
) -> Result<impl warp::Reply, warp::Rejection> {
    info!("Recieve notification endpoint reached");

    Ok(ws.on_upgrade( move |socket| {
        info!("Upgrading");
        client_connection(socket)
        // , uid, jwt, clients
    }))
}

pub async fn client_connection(socket: warp::ws::WebSocket) {
    let (mut send, mut recv) = socket.split();

    while let Some(body) = recv.next().await {
        if let Ok(content) = body {
            info!("Got: {:?}", content);
        }

        send.send(Message::text("somebody once told me")).await.unwrap();
    }

    println!("client disconnected");
}
