mod structs;
mod api;
mod wss;

use warp::{Filter, Reply, Rejection};

pub fn api() -> impl Filter<Extract = (impl Reply, ), Error = Rejection> + Clone {
    let root = warp::path("api");

    let health = root
        .and(warp::path("health"))
        .and(warp::path::end())
        .and_then(api::health);

    health
        .or(api::notifs())
}

pub fn ws() -> impl Filter<Extract = (impl Reply, ), Error = Rejection> + Clone  {
    let root = warp::path("ws");

    let recv = root
        .and(warp::path("recv"))
        .and_then(wss::recv);

    recv
}