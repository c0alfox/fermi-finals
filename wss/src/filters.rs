use crate::*;
use warp::{Filter, path::FullPath};

pub fn log_request() -> impl Filter<Extract = (), Error = std::convert::Infallible> + Clone {
    warp::path::full()
        .map(|p: FullPath| {
            info!("REQ: {}", p.as_str());
        }).untuple_one()
}