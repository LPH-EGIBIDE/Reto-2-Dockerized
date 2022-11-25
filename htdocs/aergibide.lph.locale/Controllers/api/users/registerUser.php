<?php

require_once "../../../config.inc.php";

use Entities\UserEntity;
use Exceptions\DataNotFoundException;
use Repositories\UserRepository;
use Utils\EmailUtils;

session_start();

header('Content-Type: application/json');

if (isset($_SESSION["user"]) || isset($_SESSION["mfa_pending"])) {
    $user = $_SESSION["user"] ?? $_SESSION["mfa_pending"];
    if($user instanceof UserEntity)
        echo json_encode(["status" => "success", "user" => $user->getUsername(), "message" => "User is already logged in"]);
} else {

    try{
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";
    $email = $_POST["email"] ?? "";

    //Validate the fields
    if (empty($username) || empty($password) || empty($email)) {
        throw new Exception("Los campos no pueden estar vacios");
    }
    //Check length of the username
    if(strlen($username) < 4 || strlen($username) > 20){
        throw new Exception("El nombre de usuario debe tener entre 4 y 20 caracteres");
    }
    //Check length of the password
    if(strlen($password) < 8 || strlen($password) > 128){
        throw new Exception("La contraseña debe tener entre 8 y 128 caracteres");
    }
    //Check if the email is valid
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        throw new Exception("El email no es valido");
    }
    //Check if the username is already taken
    try {
        UserRepository::getUserByUsername($username);
        throw new Exception("El nombre de usuario ya esta en uso");
    } catch (DataNotFoundException $ignored) {}
    //Check if the email is already taken
    try {
        UserRepository::getUserByEmail($email);
        throw new Exception("El email ya esta en uso");
    } catch (DataNotFoundException $ignored) {}

    //Create the user
    $user = new UserEntity($username, $email, UserEntity::hashPassword($password), 0, "", 1, 0, 0, 2, "");
    if(!UserRepository::insertUser($user)){
        throw new Exception("Error al realizar el registro");
    }
    //Send the email
    $emailUtils = new EmailUtils(EMAIL_API_KEY);
    $emailUtils->sendRegisterEmail($user);
    echo json_encode(["status" => "success", "user" => $user->getUsername(), "message" => "Usuario registrado correctamente"]);

} catch(Exception $e){
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

}
