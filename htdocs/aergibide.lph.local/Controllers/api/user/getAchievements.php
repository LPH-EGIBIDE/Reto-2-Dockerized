<?php

use Exceptions\DataNotFoundException;
use Repositories\AchievementRepository;
use Utils\AuthUtils;

require_once "../../../config.inc.php";
session_start();
header('Content-Type: application/json');
if (!AuthUtils::checkAuth())
    die(json_encode(["status" => "error", "message" => "No hay sesión iniciada"]));

$user = $_SESSION['user'];

try {
    $achievements = AchievementRepository::getAllAchievements();
    $userAchievements = AchievementRepository::getAchievementsByUser($user);
    $achievementArray = [];
    foreach ($achievements as $achievement) {
        $achievementArray[] = $achievement->toArray();
        if (in_array($achievement, $userAchievements)) {
            $achievementArray[count($achievementArray) - 1]['awarded'] = true;
        } else {
            $achievementArray[count($achievementArray) - 1]['awarded'] = false;
        }
    }
    echo json_encode(["status" => "success", "data" => $achievementArray]);
} catch (DataNotFoundException $e) {
    die(json_encode(["status" => "error", "message" => $e->getMessage()]));
}