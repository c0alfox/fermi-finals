pub mod api;
mod wss;

use warp::{Filter, Reply, Rejection};

pub fn api() -> impl Filter<Extract = (impl Reply, ), Error = Rejection> + Clone {
    let root = warp::path("api");

    let health = root
        .and(warp::path("health"))
        .and_then(api::health);

    let send = root
        .and(warp::path("send"))
        .and_then(api::send);

    health
        .or(send)
}

pub fn ws() -> impl Filter<Extract = (impl Reply, ), Error = Rejection> + Clone  {
    let root = warp::path("ws");

    let recv = root
        .and(warp::path("recv"))
        .and_then(wss::recv);

    recv
}