mod macros;
mod routes;
mod filters;

use warp::Filter;

#[tokio::main]
async fn main() {
    info!("Application started");
    
    let preprocess = filters::log_request();
    let routes = routes::api().or(routes::ws());
    let postprocess = warp::any();

    let server = preprocess.and(routes.and(postprocess));

    info!("Server started");
    warp::serve(server).run(([0, 0, 0, 0], 8888)).await;
}