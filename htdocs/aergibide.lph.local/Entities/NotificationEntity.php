<?php

namespace Entities;


class NotificationEntity
{
    private int $id;
    private string $text;
    private bool $dismissed;
    private string $href;
    private int $type;
    private UserEntity $user;

    public function __construct(string $text, bool $dismissed, string $href, int $type, UserEntity $user) {
        $this->text = $text;
        $this->dismissed = $dismissed;
        $this->href = $href;
        $this->type = $type;
        $this->user = $user;
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
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return int
     */
    public function isDismissed(): int
    {
        return $this->dismissed;
    }

    /**
     * @param int $dismissed
     */
    public function setDismissed(int $dismissed): void
    {
        $this->dismissed = $dismissed;
    }

    /**
     * @return string
     */
    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * @param string $href
     */
    public function setHref(string $href): void
    {
        $this->href = $href;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return UserEntity
     */
    public function getUser(): UserEntity
    {
        return $this->user;
    }

    /**
     * @param UserEntity $user
     */
    public function setUser(UserEntity $user): void
    {
        $this->user = $user;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'dismissed' => $this->dismissed,
            'href' => $this->href,
            'type' => $this->type
        ];
    }

}