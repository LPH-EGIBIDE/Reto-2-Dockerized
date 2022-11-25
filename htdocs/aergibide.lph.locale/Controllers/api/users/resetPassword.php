<?php
require_once "../../../config.inc.php";

use Entities\UserEntity;
use Exceptions\DataNotFoundException;
use Exceptions\PostException;
use Repositories\UserRepository;
use Utils\EmailUtils;


session_start();

header('Content-Type: application/json');

if (isset($_POST["email"])) {
    $email = $_POST["email"];
    if (empty($email)) {
        echo json_encode(["status" => "error", "message" => "El email no puede estar vacío"]);
    } else {
        try {
            $user = UserRepository::getUserByEmail($email);
            try {
                $tokenBytes = random_bytes(32);
            } catch (Exception $e) {
                $tokenBytes = openssl_random_pseudo_bytes(32);
            }
            $token = bin2hex($tokenBytes);
            UserRepository::setPasswordResetToken($user, $token);
            $emailUtils = new EmailUtils(EMAIL_API_KEY);
            try {
                $emailUtils->sendResetPasswordEmail($user, $token);
            } catch (PostException $e) {
                if (DEBUG_MODE) {
                    echo json_encode(["status" => "error", "message" => $e->getMessage(), "line" => $e->getLine(), "file" => $e->getFile()]);
                } else {
                    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
                }
            }
            echo json_encode(["status" => "success", "message" => "Se ha enviado un email a " . $email . " para restablecer la contraseña"]);
        } catch (DataNotFoundException $e) {
            if (DEBUG_MODE) {
                echo json_encode(["status" => "error", "message" => "No existe ningún usuario con ese email", "line" => $e->getLine(), "file" => $e->getFile()]);
            } else {
                echo json_encode(["status" => "success", "message" => "Se ha enviado un email a " . $email . " para restablecer la contraseña"]);
            }
        }
    }
} else if (isset($_POST["token"]) && isset($_POST["password"])) {
    $token = $_POST["token"];
    $password = $_POST["password"];
    if (empty($token)) {
        echo json_encode(["status" => "error", "message" => "El token no puede estar vacío"]);
    } else if (strlen($password) < 8) {
        echo json_encode(["status" => "error", "message" => "La contraseña debe tener al menos 8 caracteres"]);
    } else {
        try {
            $user = UserRepository::getUserByPasswordResetToken($token);
            $user->setPassword(UserEntity::hashPassword($password));
            UserRepository::setPasswordResetToken($user, '');
            UserRepository::updateUser($user);
            echo json_encode(["status" => "success", "message" => "La contraseña se ha restablecido correctamente"]);
        } catch (DataNotFoundException $e) {
            if (DEBUG_MODE) {
                echo json_encode(["status" => "error", "message" => "Token inválido", "line" => $e->getLine(), "file" => $e->getFile()]);
            } else {
                echo json_encode(["status" => "error", "message" => "Token inválido"]);
            }
        }
    }
} else {
    echo json_encode(["status" => "error", "message" => "Token inválido"]);
}
