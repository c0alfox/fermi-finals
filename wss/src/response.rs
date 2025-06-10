pub fn api_response(
    status_code: warp::http::StatusCode,
    message: impl Into<String> + serde::Serialize,
    data: Option<serde_json::Value>
) -> Result<warp::http::Response<String>, warp::http::Error> {
    let mut body = serde_json::json!({
        "message": message
    });
    let body = body.as_object_mut().unwrap();
    
    if let Some(mut v) = data {
        if v.is_object() {
            body.append(v.as_object_mut().unwrap());
        } else {
            body.insert("data".into(), v);
        }
    }

    warp::http::Response::builder()
        .status(status_code)
        .header("Content-Type", "application/json")
        .body(serde_json::to_string_pretty(body).unwrap())
}

#[macro_export]
macro_rules! api_response {
    ($status:expr, $message:expr, $data:expr) => {
        api_response($status, $message, Some($data))
    };
    ($status:expr, $message:expr) => {
        api_response($status, $message, None)
    };
    ($status:expr, $message:expr, $data:expr; $val:expr) => {
        match api_response!($status, $message, $data) {
            Ok(v) => Ok(v),
            Err(_) => Err($val)
        }
    };
    ($status:expr, $message:expr; $val:expr) => {
        match api_response!($status, $message) {
            Ok(v) => Ok(v),
            Err(_) => Err($val)
        }
    };
}