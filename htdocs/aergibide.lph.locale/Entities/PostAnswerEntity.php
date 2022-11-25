<?php

namespace Entities;

class PostAnswerEntity
{
    private int $id;
    private UserEntity $author;
    private PostEntity $post;
    private string $message;
    private int $upvotes;
    private array $attachments;

    public function __construct(UserEntity $author, PostEntity $post, string $message, int $upvotes, array $attachments = [])
    {
        $this->author = $author;
        $this->post = $post;
        $this->message = $message;
        $this->upvotes = $upvotes;
        $this->attachments = $attachments;
    }

    // Getters and setters
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return UserEntity
     */
    public function getAuthor(): UserEntity
    {
        return $this->author;
    }

    /**
     * @param UserEntity $author
     */
    public function setAuthor(UserEntity $author): void
    {
        $this->author = $author;
    }

    /**
     * @return PostEntity
     */
    public function getPost(): PostEntity
    {
        return $this->post;
    }

    /**
     * @param PostEntity $post
     */
    public function setPost(PostEntity $post): void
    {
        $this->post = $post;
    }




    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getUpvotes(): int
    {
        return $this->upvotes;
    }

    /**
     * @param int $upvotes
     */
    public function setUpvotes(int $upvotes): void
    {
        $this->upvotes = $upvotes;
    }

    /**
     * @return array
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @param array $attachments
     */
    public function setAttachments(array $attachments): void
    {
        $this->attachments = $attachments;
    }

    /**
     * @param AttachmentEntity $attachment
     * @return void
     */
    public function addAttachment(AttachmentEntity $attachment): void
    {
        $this->attachments[] = $attachment;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'author' => $this->author->toArray(),
            'message' => $this->message,
            'upvotes' => $this->upvotes,
            'attachments' => $this->attachments
        ];
    }


}