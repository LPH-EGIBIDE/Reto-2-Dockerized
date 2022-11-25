<?php

require_once '../../../config.inc.php';
use Exceptions\DataNotFoundException;
use Repositories\PostAnswerRepository;
use Repositories\PostRepository;
use Repositories\UserRepository;
use Utils\AuthUtils;

header('Content-Type: application/json');

session_start();
header('Content-Type: application/json');
if (!AuthUtils::checkAuth())
    die(json_encode(["status" => "error", "message" => "No hay sesión iniciada"]));

$user = $_SESSION['user'];
$offset = 10;
$data =[];
$page = $_GET['page'] ?? $_POST['page'] ?? 1;
$page = intval(max($page, 1));

$method = $_GET['method'] ?? $_POST['method'] ?? 'getPosts';

$posts = [];
try {
    switch ($method){
        case "getPosts":
            $data['pages'] = ceil(PostRepository::getPostsCount() / $offset);
            $posts = PostRepository::getAllPosts($offset, $offset * ($page - 1));
            break;
        case "userPosts":
            $id = $_GET['userId'] ?? $_POST['userId'] ?? $user->getId();
            $posts = PostRepository::getPostsByUser(UserRepository::getUserById($id), $offset, $offset * ($page - 1));
            break;
        case "userAnswers":
            $id = $_GET['userId'] ?? $_POST['userId'] ?? $user->getId();
            $posts = PostAnswerRepository::getPostAnswersByUser(UserRepository::getUserById($id), $offset, $offset * ($page - 1));
            break;
        case "userFavourites":
            $id = $_GET['userId'] ?? $_POST['userId'] ?? $user->getId();
            $posts = PostAnswerRepository::getUserFavouriteAnswers(UserRepository::getUserById($id), $offset, $offset * ($page - 1));
            break;
        default:
            die(json_encode(["status" => "error", "message" => "Método no válido"]));
    }
    $data['posts'] = [];
    foreach ($posts as $post) {
        $postArr = $post->toArray();
        //Detect if the post is an answer and add the post info
        if (!isset($postArr['topic']))
            $postArr['post'] = $post->getPost()->toArray();
        $data['posts'][] = $postArr;
    }
    echo json_encode(["status" => "success", "data" => $data]);
} catch (DataNotFoundException $e) {
    if (DEBUG_MODE) {
        echo json_encode(["status" => "error", "message" => "No hay posts", "line" => $e->getLine(), "file" => $e->getFile()]);
    } else {
        echo json_encode(["status" => "error", "message" => "No hay posts"]);
    }

    $data['posts'] = [];
}

