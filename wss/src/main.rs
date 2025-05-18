mod handler;

use warp::Filter;

#[tokio::main]
async fn main() {
    let health = warp::path!("health")
        .and_then(handler::health_handler);

    let routes = health
        .with(warp::cors().allow_any_origin());

    warp::serve(routes).run(([127, 0, 0, 1], 16384)).await;
}