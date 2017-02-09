<?php
/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * A Twitter user that someone has told the analyser to follow.
 *
 * @ORM\Entity
 * @ORM\Table(name="analysis_user")
 */
class AnalysisUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="term", type="string", nullable=false)
     */
    protected $term;

    /**
     * @var Tweet[]
     *
     * @ORM\OneToMany(targetEntity="Tweet", mappedBy="analysisUserId")
     */
    protected $tweets;

    public function __construct()
    {
        $this->tweets = new ArrayCollection();
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
    public function getTerm(): string
    {
        return $this->term;
    }

    /**
     * @return Tweet[]
     */
    public function getTweets(): array
    {
        return $this->tweets->toArray();
    }

    /**
     * @return string
     */
    public function getScreenName(): string
    {
        return '@'.$this->getTerm();
    }
}
