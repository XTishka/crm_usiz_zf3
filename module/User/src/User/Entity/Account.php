<?php


namespace User\Entity;

use DateTime;

class Account {

    /**
     * Уникальный идентификатор пользователя
     * @var int
     */
    protected $userId = 0;

    /**
     * Имя пользователя
     * @var string
     */
    protected $firstName = '';

    /**
     * Фамилия пользователя
     * @var string
     */
    protected $lastName = '';

    /**
     * Адрес электронной почты пользователя
     * @var string
     */
    protected $email = '';

    /**
     * Номер телефона пользователя
     * @var string
     */
    protected $phone = '';

    /**
     * Хеш пароля пользователя
     * @var string
     */
    protected $password = '';

    /**
     * @var bool
     */
    protected $isActive = false;

    /**
     * Роль пользователя в системе
     * @var string
     */
    protected $role = 'guest';

    /**
     * Дата и время последнего обновления профиля пользователя
     * @var DateTime
     */
    protected $updated;

    /**
     * Дата и время создания профиля пользователя
     * @var DateTime
     */
    protected $created;

    /**
     * @return int
     */
    public function getUserId(): int {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getFirstName(): string {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getFullName() {
        return sprintf('%s %s', $this->firstName, $this->lastName);
    }

    /**
     * @return string
     */
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPhone(): string {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void {
        $this->phone = $phone;
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void {
        $this->isActive = $isActive;
    }

    /**
     * @return string
     */
    public function getPassword(): string {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getRole(): string {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role): void {
        $this->role = $role;
    }

    //update user set password=PASSWORD("qwePo5467dsJHsa9") where User='root';

    /**
     * @return DateTime
     */
    public function getUpdated(): DateTime {
        if (!$this->updated instanceof DateTime)
            $this->updated = new DateTime($this->updated);
        return $this->updated;
    }

    /**
     * @param DateTime $updated
     */
    public function setUpdated(DateTime $updated): void {
        $this->updated = $updated;
    }

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime {
        if (!$this->created instanceof DateTime)
            $this->created = new DateTime($this->created);
        return $this->created;
    }

    /**
     * @param DateTime $created
     */
    public function setCreated(DateTime $created): void {
        $this->created = $created;
    }

}