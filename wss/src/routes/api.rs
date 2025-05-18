use crate::*;

use warp::http::StatusCode;

pub fn health() -> impl Future<Output = Result<impl warp::Reply, warp::Rejection>> {
    info!("Received health ping");
    futures::future::ready(Ok(StatusCode::OK))
}

pub fn send() -> impl Future<Output = Result<impl warp::Reply, warp::Rejection>> {
    info!("Send notification endpoint reached");
    futures::future::ready(Ok(StatusCode::OK))
}