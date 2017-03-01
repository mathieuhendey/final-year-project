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
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="twitter_id", type="integer", nullable=false)
     */
    protected $twitterId;

    /**
     * @var string
     *
     * @ORM\Column(name="author_screen_name", type="string", nullable=false)
     */
    protected $screenName;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Tweet", mappedBy="analysisUserId")
     */
    protected $tweets;

    /**
     * @var string
     */
    protected $type;

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
    public function getTwitterId(): string
    {
        return $this->twitterId;
    }

    /**
     * @return ArrayCollection
     */
    public function getTweets()
    {
        return $this->tweets;
    }

    /**
     * @return string
     */
    public function getScreenName(): string
    {
        return $this->screenName;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'user';
    }
}
