<?php

use Entities\AttachmentEntity;
use Exceptions\DataNotFoundException;
use Repositories\AttachmentRepository;
use Utils\AuthUtils;

require '../../../config.inc.php';

session_start();

if (!AuthUtils::checkAuth())
    die(json_encode(["status" => "error", "message" => "No hay sesión iniciada"]));

$method = $_POST['method'] ?? 'get';


/**
 * @return array
 * @throws DataNotFoundException
 */
function getTutorials(): array
{
    $tutorialList = AttachmentRepository::getTutorials();
    $tutorials = [];
    foreach ($tutorialList as $tutorial) {
        $author = $tutorial->getUploadedBy();
        $tutorial = $tutorial->toArray();
        $tutorial['uploadedBy'] = $author->toArray();
        $tutorial['isDeletable'] = ($author->getId() == $_SESSION['user']->getId()) || AuthUtils::checkAdminAuth();
        $tutorials[] = $tutorial;
    }
    return $tutorials;
}

/**
 * @param AttachmentEntity $tutorial
 * @return void
 * @throws DataNotFoundException
 */
function deleteTutorial(AttachmentEntity $tutorial): void
{
    if (($tutorial->getUploadedBy()->getId() != $_SESSION['user']->getId()) && !AuthUtils::checkAdminAuth())
        throw new DataNotFoundException("No tienes permisos para eliminar este tutorial");
    AttachmentRepository::deleteAttachment($tutorial);
    unlink(getcwd() . "/uploads/" . $tutorial->getFilepath());
}
try {
    switch ($method){
        case 'get':
            $tutorials = getTutorials();
            echo json_encode(["status" => "success", "tutorials" => $tutorials]);
            break;
        case 'delete':
            $tutorialId = $_POST['tutorialId'] ?? "";
            if (empty($tutorialId))
                die(json_encode(["status" => "error", "message" => "No se ha especificado el tutorial"]));
            $tutorial = AttachmentRepository::getAttachmentById(intval($tutorialId));
            deleteTutorial($tutorial);
            die(json_encode(["status" => "success", "message" => "Tutorial eliminado correctamente"]));
            break;
        default:
            die(json_encode(["status" => "error", "message" => "Método no soportado"]));
    }

} catch (DataNotFoundException $e) {
    if (DEBUG_MODE)
        die(json_encode(["status" => "error", "message" => $e->getMessage(), "line" => $e->getLine(), "file" => $e->getFile()]));
    else
        die(json_encode(["status" => "error", "message" => "No hay tutoriales"]));
}



