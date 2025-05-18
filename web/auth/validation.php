<?php
function is_valid_email($email) {
    return !empty($email)
        && preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $email);
}

function is_valid_password($password) {
    return !empty($password)
        && preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@!_\-.*\/]).{8,}$/", $password);
}