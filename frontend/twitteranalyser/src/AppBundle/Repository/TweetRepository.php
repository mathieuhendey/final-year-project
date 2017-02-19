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
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

class TweetRepository extends EntityRepository
{
    public function findAllTweetsForTopicIdWithTweetIdGreaterThan($topicId, $tweetId)
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

    public function findAllTweetsForUserIdWithTweetIdGreaterThan($userId, $tweetId)
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
}
