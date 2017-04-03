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
    private $hashtag;

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
     * @param bool $hashtag
     */
    private function __construct(
        bool $topic = false,
        int $id = null,
        bool $rateLimited = false,
        int $timeLeftOnStream = null,
        bool $hashtag = false
    ) {
        $this->topic = $topic;
        $this->id = $id;
        $this->rateLimited = $rateLimited;
        $this->timeLeftOnStream = $timeLeftOnStream;
        $this->hashtag = $hashtag;
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
     * @param int  $id
     * @param bool $reanalysisAvailable
     *
     * @return AnalysisObject
     */
    public static function fromTopicResponse(int $id, bool $reanalysisAvailable = false): self
    {
        return new self(true, $id, false, null, false, $reanalysisAvailable);
    }

    /**
     * @param int  $id
     * @param bool $reanalysisAvailable
     *
     * @return AnalysisObject
     */
    public static function fromHashtagResponse(int $id, bool $reanalysisAvailable = false): self
    {
        return new self(true, $id, false, null, true, $reanalysisAvailable);
    }

    /**
     * @param int  $id
     * @param bool $reanalysisAvailable
     *
     * @return AnalysisObject
     */
    public static function fromUserResponse(int $id, bool $reanalysisAvailable = false): self
    {
        return new self(false, $id, false, null, false, $reanalysisAvailable);
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

    /**
     * @return bool
     */
    public function isHashtag(): bool
    {
        return $this->hashtag;
    }
}
