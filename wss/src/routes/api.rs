use super::structs::Notification;
use crate::*;

use warp::{http::StatusCode, reject::reject, Filter, Rejection, Reply};

pub fn notifs(
    db_pool: DBPoolRef,
) -> impl Filter<Extract = (impl Reply,), Error = Rejection> + Clone {
    let root = warp::path("notifs");

    let send = root
        .and(warp::path("send"))
        .and(with_dbpool(db_pool))
        .and(warp::post())
        .and(warp::body::json())
        .and_then(send);

    send
}

pub async fn health() -> Result<impl warp::Reply, warp::Rejection> {
    info!("Received health ping");
    Ok(StatusCode::OK)
}

pub async fn send(
    db_pool: DBPoolRef,
    body: Notification,
) -> Result<impl warp::Reply, warp::Rejection> {
    info!("Send notification endpoint reached, received {:?}", body);

    let conn = match acquire_from(db_pool).await {
        Ok(v) => v,
        Err(_) => return Err(reject()),
    };

    Ok(StatusCode::OK)
}
