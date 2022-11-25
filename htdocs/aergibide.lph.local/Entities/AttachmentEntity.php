<?php

namespace Entities;

use DateTime;

class AttachmentEntity
{
    private int $id;
    private string $filename;
    private string $filepath;
    private string $contentType;
    private DateTime $uploadedAt;
    private ?UserEntity $uploadedBy;
    private bool $public;
    private bool $isTutorial;

    public function __construct(string $filename, string $filepath, string $contentType, DateTime $uploadedAt, ?UserEntity $uploadedBy, int $public, int $isTutorial)
    {
        $this->filename = $filename;
        $this->filepath = $filepath;
        $this->contentType = $contentType;
        $this->uploadedAt = $uploadedAt;
        $this->uploadedBy = $uploadedBy;
        $this->public = $public;
        $this->isTutorial = $isTutorial;
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
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getFilepath(): string
    {
        return $this->filepath;
    }

    /**
     * @param string $filepath
     */
    public function setFilepath(string $filepath): void
    {
        $this->filepath = $filepath;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     */
    public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
    }

    /**
     * @return DateTime
     */
    public function getUploadedAt(): DateTime
    {
        return $this->uploadedAt;
    }

    /**
     * @param DateTime $uploadedAt
     */
    public function setUploadedAt(DateTime $uploadedAt): void
    {
        $this->uploadedAt = $uploadedAt;
    }


    /**
     * @return ?UserEntity
     */
    public function getUploadedBy(): ?UserEntity
    {
        return $this->uploadedBy;
    }

    /**
     * @param UserEntity $uploadedBy
     */
    public function setUploadedBy(UserEntity $uploadedBy): void
    {
        $this->uploadedBy = $uploadedBy;
    }

    /**
     * @return int
     */
    public function isPublic(): int
    {
        return $this->public;
    }

    /**
     * @param int $public
     */
    public function setPublic(int $public): void
    {
        $this->public = $public;
    }

    /**
     * @return int
     */

    public function isTutorial(): int
    {
        return $this->isTutorial;
    }

    /**
     * @param int $isTutorial
     */
    public function setIsTutorial(int $isTutorial): void
    {
        $this->isTutorial = $isTutorial;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'filename' => $this->filename,
            'uploadedAt' => $this->uploadedAt->format('Y-m-d H:i:s'),
            'public' => $this->public,
            'href' => '/api/attachments/id/' . $this->id,
            'isTutorial' => $this->isTutorial
        ];
    }
}