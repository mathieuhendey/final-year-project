<?php
/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Model;

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
     * AnalysisObject constructor.
     *
     * @param bool $topic
     * @param int $id
     * @param bool $rateLimited
     */
    public function __construct(bool $topic, int $id, $rateLimited = false)
    {
        $this->topic = $topic;
        $this->id = $id;
        $this->rateLimited = $rateLimited;
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
}
