<?php

namespace Repositories;

use Db\Db;
use Entities\AttachmentEntity;
use Entities\PostAnswerEntity;
use Entities\PostEntity;
use Entities\UserEntity;
use Exceptions\DataNotFoundException;
use PDO;

abstract class PostAnswerRepository
{
    /**
     * @param int $id
     * @return PostAnswerEntity
     * @throws DataNotFoundException
     */
    public static function getPostAnswerById(int $id): PostAnswerEntity
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("SELECT * FROM post_answers WHERE id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result === false) {
            throw new DataNotFoundException("Respuesta no encontrada");
        }
        $postAnswerEntity = new PostAnswerEntity(
            UserRepository::getUserById($result->author),
            PostRepository::getPostById($result->post),
            $result->message,
            self::getPostUpvotes($id),
            AttachmentRepository::getAttachmentsForPostAnswer($id)
        );
        $postAnswerEntity->setId($result->id);
        return $postAnswerEntity;
    }

    /**
     * @param int $id
     * @return int
     * @throws DataNotFoundException
     */
    public static function getPostUpvotes(int $id): int
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) as upvotes FROM post_upvotes WHERE post_answer = :id");
        $stmt->bindParam(":id", $id);
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result === false) {
            throw new DataNotFoundException("Respuesta no encontrada");
        }
        return $result->upvotes;
    }

    /**
     * @param PostAnswerEntity $postAnswerEntity
     * @return void
     */
    public static function updatePostAnswer(PostAnswerEntity $postAnswerEntity): void
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("UPDATE post_answers SET message = :message WHERE id = :id");
        $stmt->execute([
            ":message" => $postAnswerEntity->getMessage(),
            ":id" => $postAnswerEntity->getId()
        ]);
    }

    /**
     * @param PostAnswerEntity $postAnswerEntity
     * @return void
     */
    public static function deletePostAnswer(PostAnswerEntity $postAnswerEntity): void
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("DELETE FROM post_answers WHERE id = :id");
        $stmt->execute([
            ":id" => $postAnswerEntity->getId()
        ]);
    }

    /**
     * @param PostAnswerEntity $postAnswerEntity
     * @return bool
     */
    public static function createPostAnswer(PostAnswerEntity $postAnswerEntity): bool
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("INSERT INTO post_answers (author, post, message) VALUES (:author_id, :post_id, :message)");
        $stmt->execute([
            ":author_id" => $postAnswerEntity->getAuthor()->getId(),
            ":post_id" => $postAnswerEntity->getPost()->getId(),
            ":message" => $postAnswerEntity->getMessage()
        ]);

        // Get the last inserted id
        $postAnswerEntity->setId($db->lastInsertId());
        return $postAnswerEntity->getId() !== 0;
    }


    /**
     * @param PostEntity $postEntity
     * @param int $offset
     * @param int $startFrom
     * @return array
     * @throws DataNotFoundException
     */
    public static function getPostAnswersByPost(PostEntity $postEntity, int $offset = 15, int $startFrom = 0): array
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("SELECT * FROM post_answers WHERE post = :post_id LIMIT :offset OFFSET :start_from");
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->bindValue(":post_id", $postEntity->getId(), PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->bindValue(":start_from", $startFrom, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        if ($result === false) {
            throw new DataNotFoundException("Respuesta no encontrada");
        }
        $postAnswers = [];
        foreach ($result as $postAnswer) {
            $postAnswerEntity = new PostAnswerEntity(
                UserRepository::getUserById($postAnswer->author),
                $postEntity,
                $postAnswer->message,
                self::getPostUpvotes($postAnswer->id),
            );
            $postAnswerEntity->setId($postAnswer->id);
            $postAnswers[] = $postAnswerEntity;
        }
        return $postAnswers;
    }


    /**
     * @param UserEntity $userEntity
     * @param int $offset
     * @param int $startFrom
     * @return array
     * @throws DataNotFoundException
     */
    public static function getPostAnswersByUser(UserEntity $userEntity, int $offset = 15, int $startFrom = 0): array
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("SELECT * FROM post_answers WHERE author = :author_id LIMIT :offset OFFSET :start_from");
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->bindValue(":start_from", $startFrom, PDO::PARAM_INT);
        $stmt->bindValue(":author_id", $userEntity->getId(), PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        if ($result === false) {
            throw new DataNotFoundException("Respuesta no encontrada");
        }
        $postAnswers = [];
        foreach ($result as $postAnswer) {
            $postAnswerEntity = new PostAnswerEntity(
                $userEntity,
                PostRepository::getPostById($postAnswer->post),
                $postAnswer->message,
                self::getPostUpvotes($postAnswer->id),
            );
            $postAnswerEntity->setId($postAnswer->id);
            $postAnswers[] = $postAnswerEntity;
        }
        return $postAnswers;
    }

    /**
     * @param UserEntity $userEntity
     * @param PostEntity $postEntity
     * @param int $offset
     * @param int $startFrom
     * @return array
     * @throws DataNotFoundException
     */
    public static function getPostAnswersByUserAndPost(UserEntity $userEntity, PostEntity $postEntity, int $offset = 15, int $startFrom = 15): array
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("SELECT * FROM post_answers WHERE author = :author_id AND post = :post_id LIMIT :offset OFFSET :start_from");
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->bindValue(":start_from", $startFrom, PDO::PARAM_INT);
        $stmt->bindValue(":author_id", $userEntity->getId(), PDO::PARAM_INT);
        $stmt->bindValue(":post_id", $postEntity->getId(), PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        if ($result === false) {
            throw new DataNotFoundException("Respuesta no encontrada");
        }
        $postAnswers = [];
        foreach ($result as $postAnswer) {
            $postAnswerEntity = new PostAnswerEntity(
                $userEntity,
                $postEntity,
                $postAnswer->message,
                self::getPostUpvotes($postAnswer->id),
            );
            $postAnswerEntity->setId($postAnswer->id);
            $postAnswers[] = $postAnswerEntity;
        }
        return $postAnswers;
    }

    /**
     * @param UserEntity $userEntity
     * @param int $offset
     * @param int $startFrom
     * @return array
     * @throws DataNotFoundException
     */
    static function getUserFavouriteAnswers(UserEntity $userEntity, int $offset = 15, int $startFrom = 0): array
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("SELECT * FROM post_answers, user_favourite_answers WHERE post_answers.id = user_favourite_answers.answer AND user_favourite_answers.user = :author_id LIMIT :offset OFFSET :start_from");
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->bindValue(":start_from", $startFrom, PDO::PARAM_INT);
        $stmt->bindValue(":author_id", $userEntity->getId(), PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        if ($result === false) {
            throw new DataNotFoundException("Respuesta no encontrada");
        }
        $postAnswers = [];
        foreach ($result as $postAnswer) {
            $postAnswerEntity = new PostAnswerEntity(
                $userEntity,
                PostRepository::getPostById($postAnswer->post),
                $postAnswer->message,
                self::getPostUpvotes($postAnswer->id),
            );
            $postAnswerEntity->setId($postAnswer->id);
            $postAnswers[] = $postAnswerEntity;
        }
        return $postAnswers;
    }

    /**
     * @param UserEntity $userEntity
     * @param PostAnswerEntity $postAnswerEntity
     * @return bool
     */
    static function addUserFavouriteAnswer(UserEntity $userEntity, PostAnswerEntity $postAnswerEntity): bool
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("INSERT INTO user_favourite_answers (user, answer) VALUES (:user_id, :answer_id)");
        $stmt->bindValue(":user_id", $userEntity->getId(), PDO::PARAM_INT);
        $stmt->bindValue(":answer_id", $postAnswerEntity->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }


    /**
     * @param UserEntity $userEntity
     * @param PostAnswerEntity $postAnswerEntity
     * @return bool
     * @throws DataNotFoundException
     */
    static function removeUserFavouriteAnswer(UserEntity $userEntity, PostAnswerEntity $postAnswerEntity): bool
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("DELETE FROM user_favourite_answers WHERE :user_id = user AND :answer_id = answer");
        $stmt->bindValue(":user_id", $userEntity->getId(), PDO::PARAM_INT);
        $stmt->bindValue(":answer_id", $postAnswerEntity->getId(), PDO::PARAM_INT);
        $result = $stmt->execute();
        if ($stmt->rowCount() === 0) {
            throw new DataNotFoundException("Esta respuesta no está marcada como favorita");
        }
        return $result;
    }


    /**
     * @param UserEntity $userEntity
     * @param PostAnswerEntity $postAnswerEntity
     * @return bool
     */
    public static function upvotePostAnswer(UserEntity $userEntity, PostAnswerEntity $postAnswerEntity): bool
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("INSERT INTO post_upvotes (user, post_answer) VALUES (:user_id, :post_answer_id)");
        return $stmt->execute([
            ":user_id" => $userEntity->getId(),
            ":post_answer_id" => $postAnswerEntity->getId()
        ]);
    }

    /**
     * @param UserEntity $userEntity
     * @param PostAnswerEntity $postAnswerEntity
     * @return bool
     */
    public static function getUpvote(UserEntity $userEntity, PostAnswerEntity $postAnswerEntity): bool
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM post_upvotes WHERE user = :user_id AND post_answer = :post_answer_id");
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->execute([
            ":user_id" => $userEntity->getId(),
            ":post_answer_id" => $postAnswerEntity->getId()
        ]);
        $result = $stmt->fetch();
        return $result->count > 0;
    }

    /**
     * @param UserEntity $userEntity
     * @param PostAnswerEntity $postAnswerEntity
     * @return bool
     */
    public static function downvotePostAnswer(UserEntity $userEntity, PostAnswerEntity $postAnswerEntity): bool
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("DELETE FROM post_upvotes WHERE user = :user_id AND post_answer = :post_answer_id");
        return $stmt->execute([
            ":user_id" => $userEntity->getId(),
            ":post_answer_id" => $postAnswerEntity->getId()
        ]);
    }

    /**
     * @param PostAnswerEntity $postAnswerEntity
     * @param AttachmentEntity $attachmentEntity
     * @return bool
     */
    public static function addAttachment(PostAnswerEntity $postAnswerEntity, AttachmentEntity $attachmentEntity): bool
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("INSERT INTO post_answer_attachments (post_answers_id, attachments_id) VALUES (:post_answer_id, :attachment_id)");
        return $stmt->execute([
            ":post_answer_id" => $postAnswerEntity->getId(),
            ":attachment_id" => $attachmentEntity->getId()
        ]);
    }

    /**
     * @param PostAnswerEntity $postAnswerEntity
     * @return array
     * @throws DataNotFoundException
     */
    public static function getAttachments(PostAnswerEntity $postAnswerEntity): array
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("SELECT * FROM post_answer_attachments WHERE post_answers_id = :post_answer_id");
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->execute([
            ":post_answer_id" => $postAnswerEntity->getId()
        ]);
        $result = $stmt->fetchAll();
        if ($result === false) {
            throw new DataNotFoundException("Respuesta no encontrada");
        }
        $attachments = [];
        foreach ($result as $attachment) {
            $attachments[] = AttachmentRepository::getAttachmentById($attachment->attachments_id);
        }
        return $attachments;
    }


    /**
     * @param UserEntity $user
     * @return int
     * @throws DataNotFoundException
     */
    public static function getPostAnswerCountByUser(UserEntity $user):int
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM post_answers WHERE author = :user_id");
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->execute([
            ":user_id" => $user->getId()
        ]);
        $result = $stmt->fetch();
        if ($result === false) {
            throw new DataNotFoundException("Respuesta no encontrada");
        }
        return $result->count;
    }

    /**
     * @param UserEntity $user
     * @return int
     * @throws DataNotFoundException
     */
    public static function getUpvoteCountByUser(UserEntity $user):int
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM post_upvotes WHERE user = :user_id");
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->execute([
            ":user_id" => $user->getId()
        ]);
        $result = $stmt->fetch();
        if ($result === false) {
            throw new DataNotFoundException("Error al obtener el número de votos");
        }
        return $result->count;
    }

    /**
     * @param UserEntity $user
     * @return int
     * @throws DataNotFoundException
     */
    public static function getFavouriteCountByUser(UserEntity $user):int
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM user_favourite_answers WHERE user = :user_id");
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->execute([
            ":user_id" => $user->getId()
        ]);
        $result = $stmt->fetch();
        if ($result === false) {
            throw new DataNotFoundException("Error al obtener el número de favoritos");
        }
        return $result->count;
    }

    /**
     * @param PostAnswerEntity $postAnswerEntity
     * @return int
     * @throws DataNotFoundException
     */
    public static function getFavouriteCount(PostAnswerEntity $postAnswerEntity):int
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM user_favourite_answers WHERE answer = :answer_id");
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->execute([
            ":answer_id" => $postAnswerEntity->getId()
        ]);
        $result = $stmt->fetch();
        if ($result === false) {
            throw new DataNotFoundException("Respuesta no encontrada");
        }
        return $result->count;
    }

    /**
     * @param UserEntity $userEntity
     * @param PostAnswerEntity $postAnswerEntity
     * @return bool
     */
    public static function getFavorite(UserEntity $userEntity, PostAnswerEntity $postAnswerEntity):bool
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM user_favourite_answers WHERE user = :user_id AND answer = :answer_id");
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->execute([
            ":user_id" => $userEntity->getId(),
            ":answer_id" => $postAnswerEntity->getId()
        ]);
        $result = $stmt->fetch();
        return $result->count > 0;
    }


}