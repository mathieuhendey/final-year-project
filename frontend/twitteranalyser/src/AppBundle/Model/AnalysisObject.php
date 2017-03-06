<?php

/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Model;

/**
 * Represents the response from the Python API.
 */
class AnalysisObject
{
    /**
     * @var bool
     */
    private $topic;

    /**
     * @var bool
     */
    private $rateLimited;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $timeLeftOnStream;

    /**
     * AnalysisObject constructor.
     *
     * @param bool $topic
     * @param int  $id
     * @param bool $rateLimited
     * @param int  $timeLeftOnStream
     */
    private function __construct(
        bool $topic = false,
        int $id = null,
        bool $rateLimited = false,
        int $timeLeftOnStream = null
    ) {
        $this->topic = $topic;
        $this->id = $id;
        $this->rateLimited = $rateLimited;
        $this->timeLeftOnStream = $timeLeftOnStream;
    }

    /**
     * @param int $timeLeftOnStream
     *
     * @return AnalysisObject
     */
    public static function fromRateLimitedResponse(int $timeLeftOnStream): self
    {
        return new self(false, 0, true, $timeLeftOnStream);
    }

    /**
     * @param int $id
     *
     * @return AnalysisObject
     */
    public static function fromTopicResponse(int $id): self
    {
        return new self(true, $id, false, null);
    }

    /**
     * @param int $id
     *
     * @return AnalysisObject
     */
    public static function fromUserResponse(int $id): self
    {
        return new self(false, $id, false, null);
    }

    /**
     * @return bool
     */
    public function isTopic(): bool
    {
        return $this->topic;
    }

    /**
     * @return bool
     */
    public function isUser(): bool
    {
        return !$this->topic;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isRateLimited(): bool
    {
        return $this->rateLimited;
    }

    /**
     * @return int
     */
    public function getTimeLeftOnStream(): int
    {
        return $this->timeLeftOnStream;
    }
}
