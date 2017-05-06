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
use Symfony\Component\Validator\Constraints\Date;

/**
 * A topic, as opposed to a user.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AnalysisTopicRepository")
 * @ORM\Table(name="analysis_topic")
 */
class AnalysisTopic implements AnalysisEntityInterface
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
     * @var bool
     *
     * @ORM\Column(name="is_hashtag", type="boolean", nullable=false)
     */
    protected $hashtag;

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
     * @ORM\OneToMany(targetEntity="Tweet", mappedBy="analysisTopicId")
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
    public function getTerm(): string
    {
        return $this->term;
    }

    /**
     * @return Collection|ArrayCollection
     */
    public function getTweets(): Collection
    {
        return new ArrayCollection(array_reverse($this->tweets->toArray()));
    }

    /**
     * @return bool
     */
    public function isHashtag(): bool
    {
        return $this->hashtag;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->isHashtag() ? self::HASHTAG_TYPE : self::TOPIC_TYPE;
    }

    /**
     * @return string
     */
    public function getPrettyTerm(): string
    {
        return $this->isHashtag() ? '#'.$this->getTerm() : $this->getTerm();
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
        return (int) floor($this->getNumberOfPositiveTweets() / $this->getNumberOfTweets() * 100);
    }

    /**
     * @return int
     */
    public function getNormalisedNumberOfNegativeTweets(): int
    {
        return 100 - $this->getNormalisedNumberOfPositiveTweets();
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

    public function getDataForLastTwelveHours(): array
    {
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $dateA = new DateTime('@'.(strtotime('-'.$i.' hour') + 3600));
            $dateB = new DateTime('@'.(strtotime('-'.($i-1).' hour') + 3600));

            $positiveCriteria = Criteria::create()
                ->where(Criteria::expr()->eq('sentiment', 'positive'))
                ->andWhere(Criteria::expr()->gt('created_on', $dateA))
                ->andWhere(Criteria::expr()->lt('created_on', $dateB));

            $totalCriteria = Criteria::create()
                ->where(Criteria::expr()->gt('created_on', $dateA))
                ->andWhere(Criteria::expr()->lt('created_on', $dateB));

            $positiveTweets = count($this->getTweets()->matching($positiveCriteria));
            $totalTweets = count($this->getTweets()->matching($totalCriteria));

            if ($totalTweets > 0) {
                $score = (int) floor($positiveTweets / $totalTweets * 100);
            } else {
                $score = 0;
            }

            array_push($data, $score);
        }

        return array_reverse($data);
    }
}
