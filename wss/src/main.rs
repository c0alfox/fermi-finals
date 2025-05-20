mod filters;
mod macros;
mod routes;

use filters::*;
use routes::db::*;
use warp::Filter;

#[tokio::main]
async fn main() {
    info!("Application started");

    let db_pool = match connect().await {
        Ok(v) => {
            info!("Connection to database successful");
            v
        }
        Err(_) => {
            error!("Connection to database failed");
            std::process::exit(1);
        }
    };
    let db_pool: DBPoolRef = Box::leak(Box::new(db_pool));

    let preprocess = filters::log_request();
    let routes = routes::api(&db_pool).or(routes::ws(&db_pool));
    info!("Routes registered");

    let server = preprocess
        .and(routes)
        .with(warp::cors().allow_any_origin())
        .recover(routes::recover);

    info!("Server started");
    warp::serve(server).run(([0, 0, 0, 0], 8888)).await;
}
