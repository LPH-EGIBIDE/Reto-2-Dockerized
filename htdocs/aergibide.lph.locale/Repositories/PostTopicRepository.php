<?php

namespace Repositories;

use Db\Db;
use Entities\PostTopicEntity;
use Exceptions\DataNotFoundException;
use PDO;

abstract class PostTopicRepository
{


    /**
     * @param int $id
     * @return PostTopicEntity
     * @throws DataNotFoundException
     */
    public static function getPostTopicById(int $id): PostTopicEntity
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("SELECT * FROM post_topics WHERE id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result === false) {
            throw new DataNotFoundException("Post topic no encontrado");
        }
        $postTopicEntity = new PostTopicEntity($result->name, $result->description);
        $postTopicEntity->setId($result->id);
        return $postTopicEntity;
    }

    /**
     * @param PostTopicEntity $postTopicEntity
     * @return void
     */
    public static function updatePostTopic(PostTopicEntity $postTopicEntity): void
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("UPDATE post_topics SET name = :name, description = :description WHERE id = :id");
        $stmt->execute([
            ":name" => $postTopicEntity->getName(),
            ":description" => $postTopicEntity->getDescription(),
            ":id" => $postTopicEntity->getId()
        ]);
    }

    /**
     * @param PostTopicEntity $postTopicEntity
     * @return void
     */
    public static function deletePostTopic(PostTopicEntity $postTopicEntity): void
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("DELETE FROM post_topics WHERE id = :id");
        $stmt->execute([
            ":id" => $postTopicEntity->getId()
        ]);
    }

    /**
     * @param PostTopicEntity $postTopicEntity
     * @return void
     */
    public static function createPostTopic(PostTopicEntity $postTopicEntity): void
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("INSERT INTO post_topics (name, description) VALUES (:name, :description)");
        $stmt->execute([
            ":name" => $postTopicEntity->getName(),
            ":description" => $postTopicEntity->getDescription()
        ]);
    }

    /**
     * @return array
     */
    public static function getAllPostTopics(): array
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("SELECT * FROM post_topics ");
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $postTopicEntities = [];
        foreach ($result as $postTopic) {
            $postTopicEntity = new PostTopicEntity($postTopic->name, $postTopic->description);
            $postTopicEntity->setId($postTopic->id);
            $postTopicEntities[] = $postTopicEntity;
        }
        return $postTopicEntities;
    }

}