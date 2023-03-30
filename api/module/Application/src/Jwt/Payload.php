<?php

namespace Application\Jwt;

use stdClass;

class Payload
{
    private string $sub;

    private string $name;

    private bool $admin;

    private string $issuedAt;

    private string $expiration;

    private string $audience;

    private int $connection;

    private int $game;

    /**
     * @param stdClass $data
     */
    public function __construct(stdClass $data)
    {
        $this->sub        = $data->sub ?? '';
        $this->name       = $data->name ?? '';
        $this->admin      = $data->admin ?? false;
        $this->issuedAt   = $data->issued_at ?? '';
        $this->expiration = $data->expiration ?? '';
        $this->audience   = $data->audience ?? '';
        $this->connection = $data->connection ?? 0;
        $this->game       = $data->game ?? 0;
    }

    /**
     * Get the value of sub
     */
    public function getSub(): string
    {
        return $this->sub;
    }

    /**
     * Set the value of sub
     *
     * @param $sub
     * @return  self
     */
    public function setSub($sub): static
    {
        $this->sub = $sub;
        return $this;
    }



    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param $name
     * @return  self
     */
    public function setName($name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of admin
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Set the value of admin
     *
     * @param $admin
     * @return  self
     */
    public function setAdmin($admin): static
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get the value of issuedAt
     */
    public function getIssuedAt()
    {
        return $this->issuedAt;
    }

    /**
     * Set the value of issuedAt
     *
     * @param $issuedAt
     * @return  self
     */
    public function setIssuedAt($issuedAt): static
    {
        $this->issuedAt = $issuedAt;

        return $this;
    }

    /**
     * Get the value of expiration
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * Set the value of expiration
     *
     * @param $expiration
     * @return  self
     */
    public function setExpiration($expiration): static
    {
        $this->expiration = $expiration;

        return $this;
    }

    /**
     * Get the value of audience
     */
    public function getAudience()
    {
        return $this->audience;
    }

    /**
     * Set the value of audience
     *
     * @param $audience
     * @return  self
     */
    public function setAudience($audience): static
    {
        $this->audience = $audience;

        return $this;
    }

    /**
     * @return string
     */
    public function getConnection(): string
    {
        return $this->connection;
    }

    /**
     * @param string $connection
     * @return Payload
     */
    public function setConnection(string $connection): Payload
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @return int
     */
    public function getGame(): int
    {
        return $this->game;
    }

    /**
     * @param int $game
     * @return Payload
     */
    public function setGame(int $game): Payload
    {
        $this->game = $game;
        return $this;
    }
}
