<?php

namespace Repositories;

use Entities\AchievementEntity;
use Db\Db;
use Entities\UserEntity;
use Exceptions\DataNotFoundException;
use PDO;


abstract class AchievementRepository
{

    /**
     * @param int $id
     * @return AchievementEntity
     * @throws DataNotFoundException
     */
    public static function getAchievementById(int $id): AchievementEntity
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("SELECT * FROM achievements WHERE id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result === false) {
            throw new DataNotFoundException("Achievement no encontrado");
        }
        $achievementEntity = new AchievementEntity($result->title, $result->description, $result->points_awarded, json_decode($result->requirements, true), AttachmentRepository::getAttachmentById($result->photo));
        $achievementEntity->setId($result->id);
        return $achievementEntity;
    }

    /**
     * @param AchievementEntity $achievementEntity
     * @return void
     */
    public static function updateAchievement(AchievementEntity $achievementEntity): void
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("UPDATE achievements SET title = :name, description = :description, points_awarded = :points, photo = :photo, requirements = :requirements WHERE id = :id");
        $stmt->execute([
            ":name" => $achievementEntity->getTitle(),
            ":description" => $achievementEntity->getDescription(),
            ":points" => $achievementEntity->getPointsAwarded(),
            ":photo" => $achievementEntity->getPhoto(),
            ":id" => $achievementEntity->getId(),
            ":requirements" => json_encode($achievementEntity->getRequirements())

        ]);
    }

    /**
     * @param AchievementEntity $achievementEntity
     * @return void
     */
    public static function deleteAchievement(AchievementEntity $achievementEntity): void
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("DELETE FROM achievements WHERE id = :id");
        $stmt->execute([
            ":id" => $achievementEntity->getId()
        ]);
    }

    /**
     * @param AchievementEntity $achievementEntity
     * @return void
     */
    public static function createAchievement(AchievementEntity $achievementEntity): void
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("INSERT INTO achievements (title, description, points_awarded, photo, requirements) VALUES (:name, :description, :points, :photo, :requirements)");
        $stmt->execute([
            ":name" => $achievementEntity->getTitle(),
            ":description" => $achievementEntity->getDescription(),
            ":points" => $achievementEntity->getPointsAwarded(),
            ":photo" => $achievementEntity->getPhoto(),
            ":requirements" => json_encode($achievementEntity->getRequirements())
        ]);
    }

    /**
     * @return array
     * @throws DataNotFoundException
     */
    public static function getAllAchievements(): array
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("SELECT * FROM achievements");
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $achievements = [];
        foreach ($result as $achievement) {
            $achievementEntity = new AchievementEntity($achievement->title, $achievement->description, $achievement->points_awarded, json_decode($achievement->requirements, true), AttachmentRepository::getAttachmentById($achievement->photo));
            $achievementEntity->setId($achievement->id);
            $achievements[] = $achievementEntity;
        }
        return $achievements;
    }

    /**
     * @param UserEntity $userEntity
     * @param AchievementEntity $achievementEntity
     * @return void
     */
    public static function awardAchievement(UserEntity $userEntity, AchievementEntity $achievementEntity): void
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("INSERT INTO user_achievements (user, achievement) VALUES (:user_id, :achievement_id)");
        $stmt->execute([
            ":user_id" => $userEntity->getId(),
            ":achievement_id" => $achievementEntity->getId()
        ]);
    }

    /**
     * @param UserEntity $userEntity
     * @param AchievementEntity $achievementEntity
     * @return void
     */
    public static function revokeAchievement(UserEntity $userEntity, AchievementEntity $achievementEntity): void
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("DELETE FROM user_achievements WHERE user = :user_id AND achievement = :achievement_id");
        $stmt->execute([
            ":user_id" => $userEntity->getId(),
            ":achievement_id" => $achievementEntity->getId()
        ]);
    }

    /**
     * @param UserEntity $userEntity
     * @return array
     * @throws DataNotFoundException
     */
    public static function getAchievementsByUser(UserEntity $userEntity): array
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("SELECT * FROM user_achievements WHERE user = :user_id");
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->execute([
            ":user_id" => $userEntity->getId()
        ]);
        $result = $stmt->fetchAll();
        $achievements = [];
        foreach ($result as $achievement) {
            $achievements[] = self::getAchievementById($achievement->achievement);
        }
        return $achievements;
    }







}