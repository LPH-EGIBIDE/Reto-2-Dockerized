<?php
require_once '../../../config.inc.php';


use Exceptions\DataNotFoundException;
use Repositories\PostRepository;
use Utils\AuthUtils;


session_start();
header('Content-Type: application/json');
if (!AuthUtils::checkAuth())
    die(json_encode(["status" => "error", "message" => "No hay sesión iniciada"]));

$page = $_GET["page"] ?? $_POST["page"] ??  1;
$offset =  10;
$startFrom = 10 * ($page - 1);
$title = $_GET['title'] ?? $_POST['title'] ?? "";
$topic = intval($_GET['topic'] ?? $_POST['topic'] ?? -1) <= -1 ? null : intval($_GET['topic'] ?? $_POST['topic'] ?? -1);
$sort = $_GET['sort'] ?? $_POST['sort'] ?? "mostRecent";

$data =[];
try {
    $sortTypes = [
        "mostRecent" => [
            "sort" => "DATE",
            "sortOrder" => "DESC"
        ],
        "leastRecent" => [
            "sort" => "DATE",
            "sortOrder" => "ASC"
        ],
        "mostViews" => [
            "sort" => "VIEWS",
            "sortOrder" => "DESC"
        ],
        "leastViews" => [
            "sort" => "VIEWS",
            "sortOrder" => "ASC"
        ]
    ];
    $sort = $sortTypes[$sort] ?? $sortTypes["mostRecent"];
    $posts = PostRepository::filterPosts($offset, $startFrom, $title, $topic, $sort["sort"], $sort["sortOrder"]);
    $data['posts'] = $posts;
    $data['postCount'] = count($posts);
    $data['status'] = "success";

} catch (DataNotFoundException $e) {
    die(json_encode(["status" => "error", "message" => $e->getMessage()]));
}

echo json_encode($data);