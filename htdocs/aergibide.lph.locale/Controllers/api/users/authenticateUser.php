<?php
require_once "../../../config.inc.php";

use Entities\NotificationEntity;
use Entities\UserEntity;
use Exceptions\DataNotFoundException;
use Exceptions\PostException;
use Repositories\NotificationRepository;
use Repositories\UserRepository;
use Utils\EmailUtils;

session_start();
header('Content-Type: application/json');
if (isset($_SESSION["user"])) {
    $user = $_SESSION["user"];
    if($user instanceof UserEntity)
        echo json_encode(["status" => "success", "user" => $user->getUsername(), "message" => "User is already logged in"]);
} else {
    //Check if the user is pending to verify the mfa code and unset the session mfa_pending
    if (isset($_SESSION["mfa_pending"])) {
        unset($_SESSION["mfa_pending"]);
    }

    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    try {
        if (empty($username) || empty($password)) {
            throw new Exception("Los campos no pueden estar vacios". $username . $password);
        }
        $user = UserRepository::getUserByUsername($username);
        if (!$user->isActive()){
            throw new Exception("El usuario esta deshabilitado, contacte con el administrador");
        }
        if($user->checkPassword($password)){
            switch ($user->getMfaType()){
                case 0:
                    $_SESSION["user"] = $user;
                    echo json_encode(["status" => "success", "user" => $user->getUsername(), "message" => "Bienvenido de nuevo ".$user->getUsername()]);
                    // Instance a new Emailutils and send a login email
                    $emailUtils = new EmailUtils(EMAIL_API_KEY);
                    $emailUtils->sendLoginEmail($user);
                    NotificationRepository::insertNotification(new NotificationEntity("Inicio de sesión" , false, "#", 0, $user), $user);
                    break;
                case 1:
                    // TOTP MFA
                    $_SESSION["mfa_pending"] = $user;
                    echo json_encode(["status" => "continueLogin", "user" => $user->getUsername(), "message" => "Introduce el codigo de verificacion de tu aplicacion de autenticacion"]);
                    break;
                case 2:
                    // Email verification
                    //Generate a 6-digit code and send it to the user email
                    $code = rand(100000, 999999);
                    $user->setMfaData($code);
                    UserRepository::updateUser($user);

                    // Instance a new Emailutils and send a login email
                    $emailUtils = new EmailUtils(EMAIL_API_KEY);
                    $emailUtils->sendMfaEmail($user);
                    $_SESSION["mfa_pending"] = $user;
                    echo json_encode(["status" => "continueLogin", "user" => $user->getUsername(), "message" => "Introduce el codigo de verificacion que te hemos enviado a tu email"]);
                    break;
            }



        } else {
            echo json_encode(["status" => "error", "message" => "Contraseña incorrecta"]);
        }
    } catch (DataNotFoundException | PostException $e) {
        //Prevent user enumeration
        //Show the exception message on json if debug mode is enabled
        if (DEBUG_MODE) {
            echo json_encode(["status" => "error", "message" => $e->getMessage(), "line" => $e->getLine(), "file" => $e->getFile()]);
        } else {
            echo json_encode(["status" => "error", "message" => "Contraseña incorrecta"]);
        }
    } catch (Exception $e) {
        if (DEBUG_MODE) {
            echo json_encode(["status" => "error", "message" => $e->getMessage(), "line" => $e->getLine(), "file" => $e->getFile()]);
        } else
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }

}
