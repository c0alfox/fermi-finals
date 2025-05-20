pub mod db;

mod api;
mod structs;
mod wss;

use crate::{log, warn};

use db::DBPoolRef;
use warp::{Filter, Rejection, Reply};

pub fn api(
    db_pool: DBPoolRef,
) -> impl Filter<Extract = (impl Reply,), Error = Rejection> + Clone {
    let root = warp::path("api");

    let health = root
        .and(warp::path("health"))
        .and(warp::path::end())
        .and_then(api::health);

    let notifs = root.and(api::notifs(db_pool));

    health.or(notifs)
}

pub fn ws(
    _db_pool: DBPoolRef,
) -> impl Filter<Extract = (impl Reply,), Error = Rejection> + Clone {
    let root = warp::path("ws");

    let recv = root.and(warp::path("recv")).and_then(wss::recv);

    recv
}

pub async fn recover(_r: Rejection) -> Result<impl Reply, std::convert::Infallible> {
    warn!("Route not matched");
    Ok(warp::http::StatusCode::NOT_FOUND)
}