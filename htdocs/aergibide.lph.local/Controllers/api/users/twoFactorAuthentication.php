<?php
require_once "../../../config.inc.php";

use Entities\UserEntity;
use Exceptions\PostException;
use Repositories\UserRepository;
use Utils\EmailUtils;

session_start();

 header('Content-Type: application/json');

if (isset($_SESSION["mfa_pending"])) {
    $user = $_SESSION["mfa_pending"];
    if ($user instanceof UserEntity) {
        $mfaCode = $_POST["mfaCode"] ?? "";
        if (empty($mfaCode)) {
            echo json_encode(["status" => "error", "message" => "El código no puede estar vacío"]);
        } else {
            if ($user->checkMfaCode($mfaCode)) {
                unset($_SESSION["mfa_pending"]);
                $_SESSION["user"] = $user;
                if (!$user->isEmailVerified()){
                    $user->setEmailVerified(true);
                    $user->setMfaType(0);
                    UserRepository::updateUser($user);
                }
                echo json_encode(["status" => "success", "user" => $user->getUsername(), "message" => "Bienvenido de nuevo " . $user->getUsername()]);
                // Instance a new Emailutils and send a login email
                $emailUtils = new EmailUtils(EMAIL_API_KEY);
                try {
                    $emailUtils->sendLoginEmail($user);
                } catch (PostException $e) {
                    die(json_encode(["status" => "error", "message" => $e->getMessage()]));
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Código incorrecto"]);
            }

        }
    }
} else {
    echo json_encode(["status" => "error", "message" => "No hay sesión iniciada o no se requiere autenticación de dos factores"]);
}