use crate::*;
use super::structs::Notification;
use warp::{Filter, Reply, Rejection, http::StatusCode};

pub fn notifs() -> impl Filter<Extract = (impl Reply, ), Error = Rejection> + Clone {
    let root = warp::path("notifs");

    let send = root
        .and(warp::path("send"))
        .and(warp::post())
        .and(warp::body::json())
        .and_then(send);

    send
}

pub fn health() -> impl Future<Output = Result<impl warp::Reply, warp::Rejection>> {
    info!("Received health ping");
    futures::future::ready(Ok(StatusCode::OK))
}

pub fn send(body: Notification) -> impl Future<Output = Result<impl warp::Reply, warp::Rejection>> {
    info!("Send notification endpoint reached, received {:?}", body);
    futures::future::ready(Ok(StatusCode::OK))
}