#[derive(serde::Serialize, serde::Deserialize, std::fmt::Debug, sqlx::FromRow, sqlx::Decode, sqlx::Encode)]
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