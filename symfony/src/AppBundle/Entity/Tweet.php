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
 * @ORM\Table(name="tweets",uniqueConstraints={@ORM\UniqueConstraint(name="id_UNIQUE", columns={"id"})})
 */
class Tweet
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="tweet_id", type="string", nullable=false)
     */
    protected $tweetId;

    /**
     * @var string
     *
     * @ORM\Column(name="in_reply_to_status_id", type="string")
     */
    protected $inReplyToStatusId;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string")
     */
    protected $text;

    /**
     * @var string
     *
     * @ORM\Column(name="in_reply_to_user_id", type="string")
     */
    protected $inReplyToUserId;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string")
     */
    protected $author;

    /**
     * @var string
     *
     * @ORM\Column(name="author_id", type="string")
     */
    protected $authorId;

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
    public function getTweetId(): string
    {
        return $this->tweetId;
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
    public function getText(): string
    {
        return $this->text;
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
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getAuthorId(): string
    {
        return $this->authorId;
    }

}