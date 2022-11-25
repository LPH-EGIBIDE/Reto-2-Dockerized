<?php

use Entities\UserEntity;
use Exceptions\DataNotFoundException;
use Repositories\NotificationRepository;
use Utils\AuthUtils;

require_once "../../../config.inc.php";
session_start();
header('Content-Type: application/json');

if (!AuthUtils::checkAuth())
    die(json_encode(["status" => "error", "message" => "No hay sesión iniciada"]));

$user = $_SESSION['user'];

function getNotifications(UserEntity $user, bool $all): array {
    $notifications = NotificationRepository::getNotificationsByUser($user, $all);
    $data = [];
    foreach ($notifications as $notification) {
        $data[] = $notification->toArray();
    }
    return $data;
}

/**
 * @throws DataNotFoundException
 */
function dismissNotification(UserEntity $user, int $id): void {
    $notification = NotificationRepository::getNotificationById($id);
    if ($notification->getUser()->getId() !== $user->getId())
        throw new DataNotFoundException("Notificación no encontrada");
    NotificationRepository::dismissNotification($notification);
}

// Get method from _POST['method'] or default to 'get'
$method = $_POST['method'] ?? 'get';
try {
    switch ($method) {
        case 'get':
            $all = $_POST['all'] ?? false;
            echo json_encode(["status" => "success", "notifications" => getNotifications($user, $all)]);
            break;
        case 'dismiss':
            $id = $_POST['id'] ?? null;
            if ($id === null)
                die(json_encode(["status" => "error", "message" => "No se ha especificado el id de la notificación"]));
            dismissNotification($user, $id);
            echo json_encode(["status" => "success", "message" => "Notificación eliminada"]);
            break;
        default:
            echo json_encode(["status" => "error", "message" => "Método no soportado"]);
            break;
    }
} catch (DataNotFoundException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}