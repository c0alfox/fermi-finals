use std::collections::HashMap;
use std::sync::{Arc, Mutex};
use sqlx::mysql::MySqlPool;
use warp::ws::Message;
use tokio::sync::mpsc;

#[derive(Debug, Clone)]
pub struct Session {
    pub jwt: String,
    pub sender: mpsc::UnboundedSender<std::result::Result<Message, warp::Error>>
}

pub type ClientSessions = Vec<Session>;
pub type Clients = Arc<Mutex<HashMap<i32, ClientSessions>>>;

pub type DBPool = MySqlPool;

pub enum DBError {
    VarError,
    SqlxError,
}

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

#[derive(
    serde::Serialize, serde::Deserialize,
    std::fmt::Debug,
    sqlx::FromRow,
    sqlx::Encode, sqlx::Decode
)]
pub struct PartialNotification {
    pub title: String,
    pub description: Option<String>,
    pub action_link: Option<String>,
    pub user_id: i32,
}