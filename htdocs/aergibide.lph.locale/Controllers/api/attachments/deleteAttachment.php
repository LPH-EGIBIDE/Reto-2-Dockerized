<?php

use Entities\AttachmentEntity;
use Exceptions\DataNotFoundException;
use Repositories\AttachmentRepository;
use Utils\AuthUtils;

require '../../../config.inc.php';

session_start();
header('Content-Type: application/json');
if (!AuthUtils::checkAuth())
    die(json_encode(["status" => "error", "message" => "No hay sesión iniciada"]));

//Upload a file, set a random name and return the name. If the file is not uploaded, return an error message

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    try {
        $attachment = AttachmentRepository::getAttachmentById($id);
        // Check if the user is the owner of the attachment or is an admin
        if (($attachment->getUploadedBy()->getId() != $_SESSION['user']->getId()) && !AuthUtils::checkAdminAuth())
            throw new DataNotFoundException("No se ha encontrado el archivo");
        // Remove the file from the server
        $attachment = AttachmentRepository::getAttachmentById($id);
        $path = getcwd() . '/uploads/' . $attachment->getFilepath();
        AttachmentRepository::deleteAttachment($attachment);
        unlink($path);
        echo json_encode(["status" => "success", "message" => "Archivo eliminado correctamente"]);
    } catch (DataNotFoundException $e) {
        if (DEBUG_MODE){
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        } else {
            echo json_encode(["status" => "error", "message" => "No se ha encontrado el archivo"]);
        }
    }
} else {
    echo json_encode(["status" => "error", "message" => "No se ha especificado el archivo a eliminar"]);
}