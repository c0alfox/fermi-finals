mod response;
mod filters;
mod macros;
mod routes;
mod types;
mod db;

use crate::types::{Clients, DBPool};
use crate::response::api_response;
use crate::db::connect;

use std::collections::HashMap;
use std::sync::{Arc, Mutex};
use std::boxed::Box;

#[tokio::main]
async fn main() {
    info!("Application started");

    let clients: Clients = Arc::new(Mutex::new(HashMap::new()));
    let clients: &'static Clients = Box::leak(Box::new(clients));

    let db_pool: DBPool = match connect().await {
        Ok(v) => {
            info!("Connection to database successful");
            v
        }
        Err(_) => {
            error!("Connection to database failed");
            std::process::exit(1);
        }
    };
    let db_pool: &'static DBPool = Box::leak(Box::new(db_pool));

    let server = routes::routes(db_pool, clients);
    
    info!("Server started");
    warp::serve(server).run(([0, 0, 0, 0], 8888)).await;
}
