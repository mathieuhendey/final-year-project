<?php
/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */
namespace AppBundle\Model;


use AppBundle\Entity\AnalysisEntityInterface;
use Doctrine\Common\Collections\Collection;

class ResultsObject
{
    /**
     * @var Collection
     */
    private $tweets;

    /**
     * @var AnalysisEntityInterface
     */
    private $term;

    /**
     * @var int
     */
    private $negativeTweets;

    /**
     * @var int
     */
    private $positiveTweets;

    /**
     * ResultsObject constructor.
     * @param Collection $tweets
     * @param AnalysisEntityInterface $term
     * @param int $negativeTweets
     * @param int $positiveTweets
     */
    public function __construct(
        Collection $tweets,
        AnalysisEntityInterface $term,
        int $negativeTweets,
        int $positiveTweets
    ) {
        $this->tweets = $tweets;
        $this->term = $term;
        $this->negativeTweets = $negativeTweets;
        $this->positiveTweets = $positiveTweets;
    }

    /**
     * @return Collection
     */
    public function getTweets(): Collection
    {
        return $this->tweets;
    }

    /**
     * @return AnalysisEntityInterface
     */
    public function getTerm(): AnalysisEntityInterface
    {
        return $this->term;
    }

    /**
     * @return int
     */
    public function getNegativeTweets(): int
    {
        return $this->negativeTweets;
    }

    /**
     * @return int
     */
    public function getPositiveTweets(): int
    {
        return $this->positiveTweets;
    }
}