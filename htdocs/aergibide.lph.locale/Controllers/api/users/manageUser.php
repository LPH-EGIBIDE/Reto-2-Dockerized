<?php

use Entities\UserEntity;
use Exceptions\DataNotFoundException;
use Repositories\UserRepository;
use Utils\AuthUtils;
use Utils\TOTP;

require_once '../../../config.inc.php';
session_start();
header("Content-Type: application/json");
if (!AuthUtils::checkAuth())
    die(json_encode(["status" => "error", "message" => "No hay sesión iniciada"]));

$user = $_SESSION["user"];

function activateMFA(UserEntity $user): void
{
    if ($user->getMfaType() == 1)
        die(json_encode(["status" => "error", "message" => "Ya tienes activado el MFA"]));
    $user->setMfaData(TOTP::generatePrivateKey());
    $user->setMfaType(1);
    UserRepository::updateUser($user);
    $_SESSION["user"] = $user;
    echo json_encode(["status" => "success", "message" => "Escanea el código QR con tu aplicación de autenticación. Este código no se volverá a mostrar", "mfaUri" => "otpauth://totp/".WEB_APP_NAME."%20-%20(".$user->getUsername().")?secret=" . $user->getMfaData()]);
}

function activateEmailMFA(UserEntity $user): void
{
    if ($user->getMfaType() == 2)
        die(json_encode(["status" => "error", "message" => "Ya tienes activado el MFA"]));
    $user->setMfaType(2);
    UserRepository::updateUser($user);
    $_SESSION["user"] = $user;
    echo json_encode(["status" => "success", "message" => "MFA por correo activado correctamente"]);
}

function deactivateMFA(UserEntity $user, string $password): void
{
    if ($user->getMfaType() == 0)
        die(json_encode(["status" => "error", "message" => "No tienes activado el MFA"]));
    if (!password_verify($password, $user->getPassword()))
        die(json_encode(["status" => "error", "message" => "La contraseña no es correcta"]));
    $user->setMfaData("");
    $user->setMfaType(0);
    UserRepository::updateUser($user);
    $_SESSION["user"] = $user;
    echo json_encode(["status" => "success", "message" => "MFA desactivado correctamente"]);
}

function changePassword(UserEntity $user, string $oldPassword, string $newPassword): void
{
    if (!password_verify($oldPassword, $user->getPassword()))
        die(json_encode(["status" => "error", "message" => "La contraseña actual no es correcta"]));
    $user->setPassword(UserEntity::hashPassword($newPassword));
    UserRepository::updateUser($user);
    $_SESSION["user"] = $user;
    echo json_encode(["status" => "success", "message" => "Contraseña cambiada correctamente"]);
}

function changeDescription(UserEntity $user, string $description): void
{
    if (strlen($description) > 4096)
        die(json_encode(["status" => "error", "message" => "La descripción no puede tener más de 4096 caracteres"]));
    $user->setProfileDescription($description);
    UserRepository::updateUser($user);
    $_SESSION["user"] = $user;
    echo json_encode(["status" => "success", "message" => "Descripción cambiada correctamente"]);
}

function reactivateAccount(UserEntity $user): void
{
    if (!AuthUtils::checkAdminAuth())
        die(json_encode(["status" => "error", "message" => "No tienes permisos para realizar esta acción"]));
    $user->setActive(true);
    UserRepository::updateUser($user);
    echo json_encode(["status" => "success", "message" => "Cuenta reactivada correctamente"]);
}

function deactivateAccount(UserEntity $user): void
{
    if (!AuthUtils::checkAdminAuth())
        die(json_encode(["status" => "error", "message" => "No tienes permisos para realizar esta acción"]));
    $user->setActive(false);
    UserRepository::updateUser($user);
    echo json_encode(["status" => "success", "message" => "Cuenta desactivada correctamente"]);
}
try {
    $method = $_POST["method"] ?? "get";
    switch ($method) {
        case "activateMFA":
            activateMFA($user);
            break;
        case "activateEmailMFA":
            activateEmailMFA($user);
            break;
        case "deactivateMFA":
            $userPassword = $_POST["password"] ?? "";
            deactivateMFA($user, $userPassword);
            break;
        case "changePassword":
            $oldPassword = $_POST["oldPassword"] ?? "";
            $newPassword = $_POST["newPassword"] ?? "";
            changePassword($user, $oldPassword, $newPassword);
            break;
        case "changeDescription":
            $description = $_POST["description"] ?? $user->getProfileDescription();
            changeDescription($user, $description);
            break;
        case "deactivateAccount":
            $userId = $_POST["userId"] ?? "";
            deactivateAccount(UserRepository::getUserById($userId));
            break;
        case "reactivateAccount":
            $userId = $_POST["userId"] ?? "";
            reactivateAccount(UserRepository::getUserById($userId));
            break;
        case "get":
            $userArray = $user->toArray();
            $userArray["email"] = $user->getEmail();
            $userArray["mfaType"] = $user->getMfaType();
            echo json_encode(["status" => "success", "data" => $userArray]);
            break;
        default:
            echo json_encode(["status" => "error", "message" => "Método no soportado"]);
            break;
    }
} catch (DataNotFoundException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}