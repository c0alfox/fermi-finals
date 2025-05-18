use warp::http::StatusCode;

type WebResult<T> = std::result::Result<T, warp::Rejection>;

pub fn health_handler() -> impl Future<Output = WebResult<impl warp::Reply>> {
    futures::future::ready(Ok(StatusCode::OK))
}