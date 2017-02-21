<?php

/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Repository;

use AppBundle\Entity\AnalysisTopic;
use AppBundle\Entity\AnalysisUser;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

class TweetRepository extends EntityRepository
{
    /**
     * @param int $topicId
     * @param int $tweetId
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function findAllTweetsForTopicIdWithTweetIdGreaterThan(int $topicId, int $tweetId)
    {
        /**
         * @var AnalysisTopic $topic
         */
        $topic = $this->getEntityManager()->find(AnalysisTopic::class, $topicId);
        $topicTweets = $topic->getTweets();

        $criteria = Criteria::create()
            ->where(Criteria::expr()->gt("id", $tweetId))
            ->andWhere(Criteria::expr()->neq("sentiment", null));

        return $topicTweets->matching($criteria);
    }

    /**
     * @param int $userId
     * @param int $tweetId
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function findAllTweetsForUserIdWithTweetIdGreaterThan(int $userId, int $tweetId)
    {
        /**
         * @var AnalysisUser $user
         */
        $user = $this->getEntityManager()->find(AnalysisUser::class, $userId);
        $userTweets = $user->getTweets();

        $criteria = Criteria::create()
            ->where(Criteria::expr()->gt("id", $tweetId))
            ->andWhere(Criteria::expr()->neq("sentiment", null));

        return $userTweets->matching($criteria);
    }

    /**
     * @param int $userId
     * @param string $sentiment
     * @return int
     */
    public function getNumberOfTweetsForUserIdWithSentiment(int $userId, string $sentiment): int
    {
        /**
         * @var AnalysisUser $user
         */
        $user = $this->getEntityManager()->find(AnalysisUser::class, $userId);
        $userTweets = $user->getTweets();

        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('sentiment', $sentiment));

        return $userTweets->matching($criteria)->count();
    }

    /**
     * @param int $topicId
     * @param string $sentiment
     * @return int
     */
    public function getNumberOfTweetsForTopicIdWithSentiment(int $topicId, string $sentiment): int
    {
        /**
         * @var AnalysisUser $topic
         */
        $topic = $this->getEntityManager()->find(AnalysisTopic::class, $topicId);
        $topicTweets = $topic->getTweets();

        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('sentiment', $sentiment));

        return $topicTweets->matching($criteria)->count();
    }
}
