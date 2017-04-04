<?php
/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * A Twitter user that someone has told the analyser to follow.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AnalysisUserRepository")
 * @ORM\Table(name="analysis_user")
 */
class AnalysisUser implements AnalysisEntityInterface
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
     * @var int
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
     * @var DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    protected $createdOn;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=false)
     */
    protected $updatedOn;

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
     * @return int
     */
    public function getTwitterId(): int
    {
        return $this->twitterId;
    }

    /**
     * @return Collection|ArrayCollection
     */
    public function getTweets(): Collection
    {
        return new ArrayCollection(array_reverse($this->tweets->toArray()));
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
        return self::USER_TYPE;
    }

    /**
     * @return string
     */
    public function getPrettyTerm(): string
    {
        return '@'.$this->getScreenName();
    }

    /**
     * @return int
     */
    public function getNumberOfTweets(): int
    {
        return $this->getTweets()->count();
    }

    /**
     * @return int
     */
    public function getNumberOfPositiveTweets(): int
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('sentiment', 'positive'));

        return $this->getTweets()->matching($criteria)->count();
    }

    /**
     * @return int
     */
    public function getNumberOfNegativeTweets(): int
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('sentiment', 'negative'));

        return $this->getTweets()->matching($criteria)->count();
    }

    /**
     * @return int
     */
    public function getNormalisedNumberOfPositiveTweets(): int
    {
        return $this->getNumberOfTweets() > 0
            ? floor($this->getNumberOfPositiveTweets() / $this->getNumberOfTweets() * 100)
            : 0;
    }

    /**
     * @return int
     */
    public function getNormalisedNumberOfNegativeTweets(): int
    {
        return $this->getNormalisedNumberOfPositiveTweets() !== 0
            ? 100 - $this->getNormalisedNumberOfPositiveTweets()
            : 0;
    }

    /**
     * @return DateTime
     */
    public function getCreatedOn(): DateTime
    {
        return $this->createdOn;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedOn(): DateTime
    {
        return $this->updatedOn;
    }
}
