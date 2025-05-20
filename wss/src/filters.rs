use crate::*;
use warp::{path::FullPath, reject::Rejection, Filter};

pub fn log_request() -> impl Filter<Extract = (), Error = std::convert::Infallible> + Clone {
    warp::path::full()
        .map(|p: FullPath| {
            info!("REQ: {}", p.as_str());
        })
        .untuple_one()
}

pub fn with_dbpool(
    db_pool: DBPoolRef,
) -> impl Filter<Extract = (DBPoolRef,), Error = std::convert::Infallible> + Clone {
    warp::any().map(move || db_pool)
}