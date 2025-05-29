mod filters;
mod macros;
mod routes;

use filters::*;
use routes::db::*;
use routes::api_response;
use warp::Filter;
use warp::ws::Message;
use tokio::sync::mpsc;

use std::sync::{Arc, Mutex};
use std::collections::HashMap;

#[derive(Debug, Clone)]
struct Client {
    pub uid: i32,
    pub sender: Option<mpsc::UnboundedSender<std::result::Result<Message, warp::Error>>>
}

type Clients = Arc<Mutex<HashMap<String, Client>>>;

#[tokio::main]
async fn main() {
    info!("Application started");

    let clients: Clients = Arc::new(Mutex::new(HashMap::new()));
    let clients: &'static Clients = Box::leak(Box::new(clients));

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
    let routes = routes::api(&db_pool).or(routes::ws(&db_pool, clients));
    info!("Routes registered");

    let server = preprocess
        .and(routes)
        .with(warp::cors().allow_any_origin())
        .recover(routes::recover);

    info!("Server started");
    warp::serve(server).run(([0, 0, 0, 0], 8888)).await;
}
