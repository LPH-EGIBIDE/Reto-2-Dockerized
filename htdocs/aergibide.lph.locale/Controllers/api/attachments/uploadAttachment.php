<?php

use Entities\AttachmentEntity;
use Entities\UserEntity;
use Exceptions\DataNotFoundException;
use Exceptions\UploadException;
use Repositories\AttachmentRepository;
use Repositories\UserRepository;
use Utils\AuthUtils;

require '../../../config.inc.php';

session_start();
header('Content-Type: application/json');
if (!AuthUtils::checkAuth())
    die(json_encode(["status" => "error", "message" => "No hay sesión iniciada"]));

$user = $_SESSION['user'];
//Upload a file, set a random name and return the name. If the file is not uploaded, return an error message

/**
 * @throws UploadException
 */
function uploadFile($file, $isTutorial = false):int{
    $file = $_FILES['file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];

    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));
    $imageTypes = [
        "jpg" => "image/jpeg",
        "jpeg" => "image/jpeg",
        "png" => "image/png",
        "gif" => "image/gif",
        "webp" => "image/webp",
        "svg" => "image/svg+xml",
        "pdf" => "application/pdf"
    ];
    $defaultType = "application/octet-stream";


    if ($fileError === 0) {
        //Check if filesize is less than 100MB
        if ($fileSize < 100000000) {
            $fileNameNew = uniqid('', true) . ".bin";
            $fileDestination = getcwd().'/uploads/' . $fileNameNew;
            $fileType = $imageTypes[$fileActualExt] ?? $defaultType;
            move_uploaded_file($fileTmpName, $fileDestination);
            // Add a new attachment to the database
            $attachment = new AttachmentEntity($fileName, $fileNameNew, $fileType, new DateTime(), $_SESSION['user'], $_POST['public'] ?? false, $isTutorial);
            return AttachmentRepository::insertAttachment($attachment);
        } else {
            throw new UploadException("El archivo es demasiado grande");
        }
    } else {
        $errorsExplicit = [
            1 => "El archivo subido excede la directiva upload_max_filesize en php.ini",
            2 => "El archivo subido excede la directiva MAX_FILE_SIZE especificada en el formulario HTML",
            3 => "El archivo subido fue sólo parcialmente cargado",
            4 => "No se subió ningún archivo",
            6 => "Falta una carpeta temporal",
            7 => "No se pudo escribir el archivo en el disco",
            8 => "Una extensión de PHP detuvo la carga de archivos"
        ];
         $errorsSimplified = [
            1 => "El archivo es demasiado grande",
            2 => "El archivo es demasiado grande",
            3 => "Error al subir el archivo Código: 103",
            4 => "No se ha seleccionado ningún archivo",
            6 => "Error al subir el archivo Código: 106",
            7 => "Error al subir el archivo Código: 107",
            8 => "Error al subir el archivo Código: 108"
         ];

        if (DEBUG_MODE){
           throw new UploadException($errorsExplicit[$fileError]);
        } else{
            throw new UploadException($errorsSimplified[$fileError]);
        }
    }
}


/**
 * @param mixed $file
 * @param UserEntity $user
 * @return void
 * @throws UploadException
 */
function setAvatar(mixed $file, UserEntity $user): void
{
    //Check if the file is an image
    $file = $_FILES['file'];
    $fileExt = explode('.', $file['name']);
    $fileExt = strtolower(end($fileExt));

    $allowedTypes = ["jpg", "jpeg", "png", "gif", "webp", "svg"];
    if (!in_array($fileExt, $allowedTypes)) {
        echo json_encode(["status" => "error", "message" => "El archivo no es una imagen ". $fileExt]);
        return;
    }
    $oldAvatar = $user->getAvatar();
    $file = uploadFile($file);
        try{
            $user->setAvatar(AttachmentRepository::getAttachmentById($file));
            UserRepository::updateUser($user);
            $uploaderId = $oldAvatar->getUploadedBy()->getId() ?? -1;
            if ($uploaderId === $user->getId()){
                AttachmentRepository::deleteAttachment($oldAvatar);
                unlink(getcwd().'/uploads/'.$oldAvatar->getFilepath());
            }
            echo json_encode(["status" => "success", "message" => "Avatar actualizado correctamente", "attachment" => $user->getAvatar()->getId()]);
        } catch (DataNotFoundException $e){
            echo json_encode(["status" => "error", "message" => "No se pudo actualizar el avatar"]);
        }
}
$action = $_POST['action'] ?? 'upload';
if (isset($_FILES['file'])) {
    try {
        switch ($action)
        {
            case 'upload':
                $id = uploadFile($_FILES['file']);
                echo json_encode(["status" => "success", "message" => "Archivo subido correctamente", "id" => $id]);
                break;
            case "setAvatar":
                setAvatar($_FILES['file'], $user);
                break;
            case "uploadTutorial":
                $file = $_FILES['file'];
                $_POST['public'] = true;
                $id = uploadFile($file, true);
                echo json_encode(["status" => "success", "message" => "Tutorial subido correctamente", "id" => $id]);
                break;
            default:
                echo json_encode(["status" => "error", "message" => "Acción no válida"]);
        }
    } catch (UploadException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
    } else {
        echo json_encode(["status" => "error", "message" => "No se ha subido ningún archivo"]);
    }