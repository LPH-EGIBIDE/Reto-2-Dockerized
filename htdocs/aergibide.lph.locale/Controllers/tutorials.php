<?php
require_once __DIR__.'/../config.inc.php';

use Utils\AuthUtils;

session_start();
$importsCss = ["/assets/stylesheets/tutorials.css"];
$importsJs = ["/assets/js/tutorials.js"];
if (!AuthUtils::checkAuth()) {
    header("Location: /login");
    exit();
}

$user = $_SESSION['user'];

require APP_ROOT.'Views/Navigation/header.php';
require APP_ROOT.'Views/tutorials.php';
require APP_ROOT.'Views/Navigation/footer.php';