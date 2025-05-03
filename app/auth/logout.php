<?php
require_once "../prelude.php";
require_once "$root/auth/authentication.php";

Auth\unset_token();
header("Location: /");
exit();