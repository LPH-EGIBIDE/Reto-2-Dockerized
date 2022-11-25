<?php

namespace Utils;

use Entities\AchievementEntity;
use Entities\NotificationEntity;
use Entities\PostEntity;
use Entities\UserEntity;
use Exceptions\DataNotFoundException;
use Repositories\AchievementRepository;
use Repositories\NotificationRepository;
use Repositories\PostAnswerRepository;
use Repositories\PostRepository;
use Repositories\UserRepository;

class AchievementManager
{
    private UserEntity $user;

    public function __construct(UserEntity $user)
    {
        $this->user = $user;
    }

    //function to check if the user has the achievement
    private function hasAchievement(int $achievementId): bool
    {
        try {
            $achievements = AchievementRepository::getAchievementsByUser($this->user);
        } catch (DataNotFoundException $e) {
            return false;
        }
        foreach ($achievements as $achievement) {
            if ($achievement->getId() == $achievementId) {
                return true;
            }
        }
        return false;
    }


    //function for checking the postAnswerQuantity achievement type
    private function checkQuantity(int $quantity, AchievementEntity $achievementEntity): void
    {
        if ($this->hasAchievement($achievementEntity->getId())) {
            return;
        }

        if ($quantity >= $achievementEntity->getRequirements()["data"]) {
            AchievementRepository::awardAchievement($this->user, $achievementEntity);
            NotificationRepository::insertNotification(new NotificationEntity("Logro desbloqueado - " . $achievementEntity->getTitle(), false, "#", 2, $this->user), $this->user);
            $this->user->setPoints($this->user->getPoints() + $achievementEntity->getPointsAwarded());
            UserRepository::updateUser($this->user);
        }
    }

    public function checkAchievements(): void
    {
        try{
            $achievements = AchievementRepository::getAllAchievements();
            $postAnswerQuantity = PostAnswerRepository::getPostAnswerCountByUser($this->user);
            $postQuantity = PostRepository::getPostCountByUser($this->user);
            $upvoteQuantity = PostAnswerRepository::getUpvoteCountByUser($this->user);
            foreach ($achievements as $achievement) {
                switch ($achievement->getRequirements()["type"]) {
                    case "postAnswerQuantity":
                        $this->checkQuantity($postAnswerQuantity, $achievement);
                        break;
                    case "postQuantity":
                        $this->checkQuantity($postQuantity, $achievement);
                        break;
                    case "upvoteQuantity":
                        $this->checkQuantity($upvoteQuantity, $achievement);
                        break;
                }
            }
        }catch(DataNotFoundException $e){
            return;
        }
    }





}