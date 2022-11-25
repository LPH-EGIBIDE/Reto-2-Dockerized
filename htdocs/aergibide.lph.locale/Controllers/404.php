<?php
require_once __DIR__.'/../config.inc.php';
use Utils\AuthUtils;
session_start();
$importsJs = [];

if (!AuthUtils::checkAuth()) {
    header("Location: /login");
    exit();
}
$user = $_SESSION['user'];

require APP_ROOT.'Views/Navigation/header.php';
require APP_ROOT.'Views/404.php';
require APP_ROOT.'Views/Navigation/footer.php';
