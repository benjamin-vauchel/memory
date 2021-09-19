<?php

class Score
{
    private int|null $id;
    private string   $name;
    private int      $time;

    /**
     * @param int|null $id
     * @param string $name
     * @param int $time
     */
    public function __construct(?int $id, string $name, int $time)
    {
        $this->id   = $id;
        $this->name = $name;
        $this->time = $time;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }

    /**
     * @param int $time
     */
    public function setTime(int $time): void
    {
        $this->time = $time;
    }


}