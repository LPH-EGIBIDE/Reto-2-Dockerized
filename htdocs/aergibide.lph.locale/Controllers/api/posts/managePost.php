<?php

require_once '../../../config.inc.php';
use Entities\PostEntity;
use Entities\UserEntity;
use Exceptions\DataNotFoundException;
use Exceptions\PostException;
use Repositories\PostRepository;
use Repositories\PostTopicRepository;
use Utils\AchievementManager;
use Utils\AuthUtils;
session_start();

if (!AuthUtils::checkAuth())
    die(json_encode(["status" => "error", "message" => "No hay sesión iniciada"]));

$user = $_SESSION["user"];


header('Content-Type: application/json');

// Get the action type
$action = $_POST['action'] ?? 'get';

// Get the post id
$postId = $_POST['postId'] ?? $_GET['postId'] ?? -1;

switch ($action){
    case 'get':
        try {
            $post = PostRepository::getPostById($postId);
            echo json_encode(["status" => "success", "data" => $post->toArray()]);
        } catch (DataNotFoundException $e) {
            if (DEBUG_MODE){
                echo json_encode(["status" => "error", "message" => $e->getMessage(), "line" => $e->getLine(), "file" => $e->getFile()]);
            } else {
                echo json_encode(["status" => "error", "message" => "El post no existe"]);
            }
        }
        break;
    case 'insert':
        $title = $_POST['title'] ?? null;
        $content = $_POST['content'] ?? null;
        $topic = $_POST['topic'] ?? 1;

        try {
            $post = insertPost($title, $content, $topic, $user);
            echo json_encode(["status" => "success", "message" => "Post insertado correctamente", "postId" => $post->getId()]);
        } catch (PostException $e) {
            if (DEBUG_MODE){
                echo json_encode(["status" => "error", "message" => $e->getMessage(), "line" => $e->getLine(), "file" => $e->getFile()]);
            } else{
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }

        }
        break;
    case 'close':
        try {
            closePost($postId);
            echo json_encode(["status" => "success", "message" => "Post cerrado correctamente"]);
        } catch (PostException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        break;
    case 'delete':
        try {
            deletePost(intval($postId));
            echo json_encode(["status" => "success", "message" => "Post eliminado correctamente"]);
        } catch (PostException $e) {
            if (DEBUG_MODE){
                echo json_encode(["status" => "error", "message" => $e->getMessage(), "line" => $e->getLine(), "file" => $e->getFile()]);
            } else{
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        }
        break;
    case 'addAttachment':
        //TODO Add topic to post
        break;
    case 'removeTopic':
        //TODO Remove topic from post
        break;
    default:
        echo json_encode(["status" => "error", "message" => "Método no soportado"]);
        break;
}



/**
 * @param string $title
 * @param string $description
 * @param int $topic
 * @param UserEntity $user
 * @return PostEntity
 * @throws PostException
 */
function insertPost(string $title, string $description, int $topic, UserEntity $user): PostEntity {
    if (empty($title) || empty($description) || empty($topic)) {
        throw new PostException("Los campos no pueden estar vacíos");
    }
    if(strlen($title) > 72){
        throw new PostException("El título no puede tener más de 72 caracteres");
    }
    if (strlen($description) > 4096) {
        throw new PostException("La descripción no puede tener más de 4096 caracteres");
    }
    try {
        $topic = PostTopicRepository::getPostTopicById($topic);
    }
    catch (DataNotFoundException $e){
        if (DEBUG_MODE){
            throw new PostException($e->getMessage()." ".$e->getFile()." ".$e->getLine());
        } else {
            throw new PostException("El tema seleccionado no existe");
        }
    }

    $post = new PostEntity($title, $description,0, $topic, $user,true, new DateTime());

    PostRepository::createPost($post);

    $achievementManager = new AchievementManager($user);
    $achievementManager->checkAchievements();

    return $post;
}


/**
 * @param int $postId
 * @return void
 * @throws PostException
 */
function deletePost(int $postId): void {
    try {
        $post = PostRepository::getPostById($postId);
        if ($post->getAuthor()->getId() != $_SESSION["user"]->getId() && !AuthUtils::checkAdminAuth()){
            throw new PostException("No tienes permisos para eliminar este post");
        }
    } catch (DataNotFoundException $e) {
        if (DEBUG_MODE){
            throw new PostException($e->getMessage()." ".$e->getFile()." ".$e->getLine());
        } else {
            throw new PostException("El post no existe");
        }
    }
    PostRepository::deletePost($post);
}


/**
 * @param int $postId
 * @return void
 * @throws PostException
 */
function closePost(int $postId): void {
    try {
        $post = PostRepository::getPostById($postId);
        if (!AuthUtils::checkAdminAuth()){
            throw new PostException("No tienes permisos para cerrar este post");
        }
        $post->setActive(false);
        PostRepository::updatePost($post);
    } catch (DataNotFoundException $e) {
        if (DEBUG_MODE){
            throw new PostException($e->getMessage()." ".$e->getFile()." ".$e->getLine());
        } else {
            throw new PostException("El post no existe");
        }
    }
}

