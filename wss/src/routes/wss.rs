use crate::*;

use warp::http::StatusCode;


pub fn recv() -> impl Future<Output = Result<impl warp::Reply, warp::Rejection>> {
    info!("Recieve notification endpoint reached");
    futures::future::ready(Ok(StatusCode::OK))
}