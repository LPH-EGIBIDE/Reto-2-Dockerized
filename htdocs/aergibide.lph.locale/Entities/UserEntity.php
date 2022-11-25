<?php

namespace Entities;
use Utils\TOTP;

class UserEntity
{
private int $id;
private string $username;
private string $email;
private string $password;
private int $type;
private string $profileDescription;
private bool $active;
private bool $emailVerified;
private int $points;
private int $mfaType;
private string $mfaData;
private ?AttachmentEntity $avatar;


public function __construct( string $username, string $email, string $password, int $type, string $profileDescription, bool $active, bool $emailVerified, int $points, int $mfaType, string $mfaData, AttachmentEntity $avatar = null)
{
$this->username = $username;
$this->email = $email;
$this->setPassword($password);
$this->type = $type;
$this->avatar = $avatar;
$this->profileDescription = $profileDescription;
$this->active = $active;
$this->emailVerified = $emailVerified;
$this->points = $points;
$this->mfaType = $mfaType;
$this->mfaData = $mfaData;
}

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
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
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
     * @return AttachmentEntity
     */
    public function getAvatar(): AttachmentEntity
    {
        return $this->avatar;
    }

    /**
     * @param AttachmentEntity $avatar
     */
    public function setAvatar(AttachmentEntity $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @return string
     */
    public function getProfileDescription(): string
    {
        return $this->profileDescription;
    }

    /**
     * @param string $profileDescription
     */
    public function setProfileDescription(string $profileDescription): void
    {
        $this->profileDescription = $profileDescription;
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
     * @return bool
     */
    public function isEmailVerified(): bool
    {
        return $this->emailVerified;
    }

    /**
     * @param bool $emailVerified
     */
    public function setEmailVerified(bool $emailVerified): void
    {
        $this->emailVerified = $emailVerified;
    }

    /**
     * @return int
     */
    public function getPoints(): int
    {
        return $this->points;
    }

    /**
     * @param int $points
     */
    public function setPoints(int $points): void
    {
        $this->points = $points;
    }


    /**
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * @param string $password
     * @return bool
     */
    public function checkPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

/**
     * @return bool
     */
    public function isMfaEnabled(): bool
    {
        return $this->mfaType !== 0;
    }

    /**
     * @return int
     */
    public function getMfaType(): int
    {
        return $this->mfaType;
    }

    /**
     * @param int $mfaType
     */
    public function setMfaType(int $mfaType): void
    {
        // 0 = disabled, 1 = TOTP, 2 = Email
        $this->mfaType = $mfaType;
    }

    /**
     * @return string
     */
    public function getMfaData(): string
    {
        return $this->mfaData;
    }

    /**
     * @param string $mfaData
     */
    public function setMfaData(string $mfaData): void
    {
        $this->mfaData = $mfaData;
    }


    //MFA stuff

    /**
     * @param string $code
     * @return bool
     */
    public function checkMfaCode(string $code): bool
    {
        if ($this->mfaType === 1) {
            return $this->checkTotpCode(intval($code));
        } elseif ($this->mfaType === 2) {
            return $this->checkEmailCode(intval($code));
        }
        return false;
    }


    /**
     * @param int $code
     * @return bool
     */
    public function checkTotpCode(int $code): bool
    {
        return TOTP::verifyTOTP($this->mfaData, $code);
    }

    /**
     * @param string $code
     * @return bool
     */
    public function checkEmailCode(string $code): bool
    {
        return $code === $this->mfaData;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'avatar' => "//". $_SERVER["SERVER_NAME"]   ."/api/attachments/id/{$this->avatar->getId()}",
            'profileDescription' => $this->profileDescription,
            'active' => $this->active,
            'points' => $this->points
        ];
    }






}