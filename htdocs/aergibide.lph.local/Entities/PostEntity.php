<?php

namespace Entities;

use DateTime;

class PostEntity
{
    private int $id;
    private string $title;
    private string $description;
    private int $views;
    private PostTopicEntity $topic;
    private UserEntity $author;
    private bool $active;
    private DateTime $date;

    public function __construct(string $title, string $description, int $views, PostTopicEntity $topic, UserEntity $author, bool $active, DateTime $date)
    {
        $this->title = $title;
        $this->description = $description;
        $this->views = $views;
        $this->topic = $topic;
        $this->author = $author;
        $this->active = $active;
        $this->date = $date;
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
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getViews(): int
    {
        return $this->views;
    }

    /**
     * @param int $views
     */
    public function setViews(int $views): void
    {
        $this->views = $views;
    }

    /**
     * @return PostTopicEntity
     */
    public function getTopic(): PostTopicEntity
    {
        return $this->topic;
    }

    /**
     * @param PostTopicEntity $topic
     */
    public function setTopic(PostTopicEntity $topic): void
    {
        $this->topic = $topic;
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
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return array
     */
    public function toArray(): array {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "views" => $this->views,
            "topic" => $this->topic->toArray(),
            "author" => $this->author->toArray(),
            "active" => $this->active,
            "date" => $this->date->format("Y-m-d H:i:s")
        ];
    }

}