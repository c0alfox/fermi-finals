mod handler;
mod macros;

use warp::Filter;

#[tokio::main]
async fn main() {
    info!("Application started");
    
    let health = warp::path!("health")
        .and_then(handler::health_handler);

    info!("Created health route");

    let routes = health
        .with(warp::cors().allow_any_origin());

    info!("Created base route");

    warp::serve(routes).run(([0, 0, 0, 0], 8888)).await;
}