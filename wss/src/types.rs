use std::collections::HashMap;
use std::sync::{Arc, Mutex};
use warp::ws::Message;
use tokio::sync::mpsc;

#[derive(Debug, Clone)]
pub struct Session {
    pub jwt: String,
    pub sender: mpsc::UnboundedSender<std::result::Result<Message, warp::Error>>
}

pub type ClientSessions = Vec<Session>;
pub type Clients = Arc<Mutex<HashMap<i32, ClientSessions>>>;

#[derive(
    serde::Serialize, serde::Deserialize,
    std::fmt::Debug,
    sqlx::FromRow,
    sqlx::Decode, sqlx::Encode
)]
pub struct Notification {
    pub notification_id: i32,
    pub title: String,
    pub description: Option<String>,
    pub action_link: Option<String>,
    pub user_id: i32,
    pub notification_datetime: chrono::NaiveDateTime
}