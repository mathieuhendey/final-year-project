<?php

/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tweet")
 */
class Tweet
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="AnalysisTopic", inversedBy="tweets")
     * @ORM\JoinColumn(name="analysis_topic_id", nullable=true)
     */
    protected $analysisTopicId;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="AnalysisUser", inversedBy="tweets")
     * @ORM\JoinColumn(name="analysis_user_id", nullable=true)
     */
    protected $analysisUserId;

    /**
     * @var string
     *
     * @ORM\Column(name="author_screen_name", type="string")
     */
    protected $authorScreenName;

    /**
     * @var string
     *
     * @ORM\Column(name="author_id", type="string")
     */
    protected $authorId;

    /**
     * @var string
     *
     * @ORM\Column(name="in_reply_to_user_id")
     */
    protected $inReplyToUserId;

    /**
     * @var string
     *
     * @ORM\Column(name="in_reply_to_screen_name", type="string")
     */
    protected $inReplyToScreenName;

    /**
     * @var string
     *
     * @ORM\Column(name="in_reply_to_status_id", type="string")
     */
    protected $inReplyToStatusId;

    /**
     * @var string
     *
     * @ORM\Column(name="tweet_id", type="string")
     */
    protected $tweetId;

    /**
     * @var string
     *
     * @ORM\Column(name="tweet_text", type="string")
     */
    protected $text;

    public function __construct()
    {
        $this->analysisTopicId = null;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAnalysisTopicId(): string
    {
        return $this->analysisTopicId;
    }

    /**
     * @return string
     */
    public function getAnalysisUserId(): string
    {
        return $this->analysisUserId;
    }

    /**
     * @return string
     */
    public function getAuthorScreenName(): string
    {
        return $this->authorScreenName;
    }

    /**
     * @return string
     */
    public function getAuthorId(): string
    {
        return $this->authorId;
    }

    /**
     * @return string
     */
    public function getInReplyToUserId(): string
    {
        return $this->inReplyToUserId;
    }

    /**
     * @return string
     */
    public function getInReplyToScreenName(): string
    {
        return $this->inReplyToScreenName;
    }

    /**
     * @return string
     */
    public function getInReplyToStatusId(): string
    {
        return $this->inReplyToStatusId;
    }

    /**
     * @return string
     */
    public function getTweetId(): string
    {
        return $this->tweetId;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
