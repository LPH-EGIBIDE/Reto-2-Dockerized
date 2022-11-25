<?php
require_once __DIR__.'/../config.inc.php';

use Repositories\PostTopicRepository;
use Utils\AuthUtils;

session_start();
$importsCss = ["/assets/stylesheets/searchPosts.css"];
$importsJs = ["/assets/js/searchPosts.js"];

if (!AuthUtils::checkAuth()) {
    header("Location: /login");
    exit();
}

$topics = PostTopicRepository::getAllPostTopics();

$user = $_SESSION['user'];

require APP_ROOT.'Views/Navigation/header.php';
require APP_ROOT.'Views/searchPosts.php';
require APP_ROOT.'Views/Navigation/footer.php';