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

pub fn api_response(
    status_code: warp::http::StatusCode,
    message: impl Into<String> + serde::Serialize,
    data: Option<serde_json::Value>
) -> Result<warp::http::Response<String>, warp::http::Error> {
    let mut body = serde_json::json!({
        "message": message
    });
    let body = body.as_object_mut().unwrap();
    
    if let Some(mut v) = data {
        if v.is_object() {
            body.append(v.as_object_mut().unwrap());
        } else {
            body.insert("data".into(), v);
        }
    }

    warp::http::Response::builder()
        .status(status_code)
        .header("Content-Type", "application/json")
        .body(serde_json::to_string_pretty(body).unwrap())
}

#[macro_export]
macro_rules! api_response {
    ($status:expr, $message:expr, $data:expr) => {
        api_response($status, $message, Some($data))
    };
    ($status:expr, $message:expr) => {
        api_response($status, $message, None)
    };
    ($status:expr, $message:expr, $data:expr; $val:expr) => {
        match api_response!($status, $message, $data) {
            Ok(v) => Ok(v),
            Err(_) => Err($val)
        }
    };
    ($status:expr, $message:expr; $val:expr) => {
        match api_response!($status, $message) {
            Ok(v) => Ok(v),
            Err(_) => Err($val)
        }
    };
}