<?php

use Exceptions\DataNotFoundException;
use Repositories\AttachmentRepository;
use Utils\AuthUtils;

require '../../../config.inc.php';

session_start();

if (!AuthUtils::checkAuth())
    die(json_encode(["status" => "error", "message" => "No hay sesión iniciada"]));

//Get the file id from the request and check if it exists
$fileId = $_GET["fileId"];
if (empty($fileId))
    die(json_encode(["status" => "error", "message" => "No se ha especificado el archivo"]));
try {
    $file = AttachmentRepository::getAttachmentById(intval($fileId));
} catch (DataNotFoundException $e) {
    if (DEBUG_MODE)
        die(json_encode(["status" => "error", "message" => "El archivo no existe", "debug" => $e->getMessage()]));
    else
        die(json_encode(["status" => "error", "message" => "El archivo no existe"]));
}

//Check if the user is the owner of the file or the file is public
if ((!$file->isPublic() && $file->getUploadedBy()->getId() != $_SESSION["user"]->getId()) && !AuthUtils::checkAdminAuth())
    if (DEBUG_MODE)
        die(json_encode(["status" => "error", "message" => "No tienes permisos para ver este archivo"]));
    else
        die(json_encode(["status" => "error", "message" => "El archivo no existe"]));


//Get the file path and check if it exists
$filePath = getcwd() . "/uploads/" . $file->getFilepath();
if (!file_exists($filePath))
    die(json_encode(["status" => "error", "message" => "El archivo no existe"]));

//set the headers to show the file
header("Content-Type: " . $file->getContentType());
header("Content-Length: " . filesize($filePath));
header("Content-Disposition: inline; filename=" . $file->getFilename());

//show the file
readfile($filePath);


