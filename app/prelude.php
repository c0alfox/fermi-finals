<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/prelude_commons.php';

require_once "$root/auth/authentication.php";
Auth\refresh_token();