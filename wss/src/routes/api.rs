use super::structs::Notification;
use crate::*;

use warp::{http::StatusCode, http::Response, Filter, Rejection, Reply};

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

    let show = root
        .and(with_dbpool(db_pool))
        .and(warp::path("show"))
        .and(warp::path::param::<u32>())
        .and(warp::get())
        .and_then(show);

    send.or(show)
}

pub async fn health() -> Result<impl warp::Reply, warp::Rejection> {
    info!("Received health ping");
    Ok(StatusCode::OK)
}

pub async fn send(
    _db_pool: DBPoolRef,
    body: Notification,
) -> Result<impl warp::Reply, warp::Rejection> {
    info!("Send notification endpoint reached, received {:?}", body);
    Ok(StatusCode::OK)
}

pub async fn show(
    db_pool: DBPoolRef,
    id: u32
) -> Result<impl warp::Reply, warp::Rejection> {
    info!("Show notification endpoint reached, received id {:?}", id);

    let res = 
        sqlx::query_as::<_, Notification>("SELECT * FROM PrgNotifications WHERE user_id = ?")
        .bind(id)
        .fetch_all(db_pool)
        .await;

    if let Err(e) = res {
        warn!("Failed to execute query, error {:?}", e);

        return Ok(
            Response::builder()
                .status(StatusCode::INTERNAL_SERVER_ERROR)
                .header("Content-Type", "application/json")
                .body("".to_string())
        );
    }

    let res = res.unwrap();

    info!("Query successful");

    Ok(
        Response::builder()
            .status(StatusCode::OK)
            .header("Content-Type", "application/json")
            .body(serde_json::to_string(&res).unwrap())
    )
}