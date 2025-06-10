use crate::*;
use crate::types::{Notification, PartialNotification};
use crate::filters::{with_dbpool};

use warp::{http::StatusCode, Filter, Rejection, Reply};

pub fn root(
    db_pool: &'static DBPool,
) -> impl Filter<Extract = (impl Reply,), Error = Rejection> + Clone {
    let root = warp::path("api");

    let health = root
        .and(warp::path("health"))
        .and(warp::path::end())
        .and_then(health);

    let notifs = root.and(notifs(db_pool));

    health.or(notifs)
}

pub fn notifs(
    db_pool: &'static DBPool,
) -> impl Filter<Extract = (impl Reply,), Error = Rejection> + Clone {
    let root = warp::path("notifs");

    let send = root
        .and(warp::path("send"))
        .and(with_dbpool(db_pool))
        .and(warp::post())
        .and(warp::body::json())
        .and_then(send)
        .recover(async |rej: Rejection| {
            if let Some(_) = rej.find::<warp::reject::MethodNotAllowed>() {
                return Ok(StatusCode::METHOD_NOT_ALLOWED);
            }

            if let Some(_) = rej.find::<warp::body::BodyDeserializeError>() {
                return Ok(StatusCode::BAD_REQUEST);
            }

            Err(warp::reject())
        });

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
    db_pool: &'static DBPool,
    body: PartialNotification
) -> Result<impl warp::Reply, warp::Rejection> {
    info!("Send notification endpoint reached, received {:?}", body);

    let res =
        sqlx::query("INSERT INTO PrgNotifications (title, description, action_link, user_id) VALUES (?, ?, ?, ?)")
        .bind(body.title)
        .bind(body.description)
        .bind(body.action_link)
        .bind(body.user_id)
        .execute(db_pool)
        .await;

    if let Err(e) = res {
        warn!("Failed to execute query, error {:?}", e);
        return api_response!(
            StatusCode::INTERNAL_SERVER_ERROR, "Errore nell'inserimento";
            warp::reject()
        )
    }

    api_response!(
        StatusCode::OK, "Notifica aggiunta con successo";
        warp::reject()
    )
}

pub async fn show(
    db_pool: &'static DBPool,
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
        return api_response!(
            StatusCode::INTERNAL_SERVER_ERROR, "Errore nell'inserimento";
            warp::reject()
        )
    }

    let res = res.unwrap();

    info!("Query successful");
    api_response!(
        StatusCode::OK, "Risultati della ricerca", serde_json::json!(res);
        warp::reject()
    )
}