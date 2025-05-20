use crate::*;

use sqlx::mysql::MySqlPool;
use std::env;

pub type DBPool = MySqlPool;
pub type DBPoolRef = &'static DBPool;

pub enum DBError {
    VarError,
    SqlxError,
}

pub async fn connect() -> Result<DBPool, DBError> {
    info!("Trying to connect to mysql database");

    let db_host = match env::var("DB_HOST") {
        Ok(s) => s,
        Err(_) => return Err(DBError::VarError),
    };

    let db_name = match env::var("DB_NAME") {
        Ok(s) => s,
        Err(_) => return Err(DBError::VarError),
    };

    let db_user = match env::var("DB_USER") {
        Ok(s) => s,
        Err(_) => return Err(DBError::VarError),
    };

    let db_passwd = match env::var("DB_PASSWORD") {
        Ok(s) => s,
        Err(_) => return Err(DBError::VarError),
    };

    info!("DB_HOST: {}", db_host);
    info!("DB_NAME: {}", db_name);
    info!("DB_USER: {}", db_user);

    let url = format!("mysql://{}:{}@{}/{}", db_user, db_passwd, db_host, db_name);

    let pool = match MySqlPool::connect(url.as_str()).await {
        Ok(p) => p,
        Err(_) => return Err(DBError::SqlxError),
    };

    Ok(pool)
}
