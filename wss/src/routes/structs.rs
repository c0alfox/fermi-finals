#[derive(serde::Serialize, serde::Deserialize, std::fmt::Debug, sqlx::FromRow, sqlx::Decode, sqlx::Encode)]
pub struct Notification {
    notification_id: i32,
    title: String,
    description: Option<String>,
    action_link: Option<String>,
    user_id: i32,
    notification_datetime: chrono::NaiveDateTime
}

#[derive(
    serde::Serialize, serde::Deserialize,
    std::fmt::Debug,
    sqlx::FromRow,
    sqlx::Encode, sqlx::Decode
)]
pub struct PartialNotification {
    title: String,
    description: Option<String>,
    action_link: Option<String>,
    user_id: i32,
}