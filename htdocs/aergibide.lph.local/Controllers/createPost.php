<?php
require_once __DIR__.'/../config.inc.php';

use Repositories\PostTopicRepository;
use Utils\AuthUtils;

session_start();

$importsCss = ['/assets/stylesheets/createQuestion.css'];
$importsJs = ["/assets/js/createQuestion.js"];

if (!AuthUtils::checkAuth()) {
    header("Location: /login");
    exit();
}

$user = $_SESSION['user'];

$topics = PostTopicRepository::getAllPostTopics();
$lastTopic = $_COOKIE['lastUsedTopicId'] ?? '';

require APP_ROOT.'Views/Navigation/header.php';
require APP_ROOT.'Views/createQuestion.php';
require APP_ROOT.'Views/Navigation/footer.php';