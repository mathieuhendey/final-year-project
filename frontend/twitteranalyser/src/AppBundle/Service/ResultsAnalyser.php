<?php

/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Service;

use AppBundle\Entity\AnalysisTopic;
use AppBundle\Entity\AnalysisUser;
use AppBundle\Model\ResultsObject;
use AppBundle\Repository\AnalysisTopicRepository;
use AppBundle\Repository\AnalysisUserRepository;
use AppBundle\Repository\TweetRepository;

class ResultsAnalyser
{
    /**
     * @var TweetRepository
     */
    private $tweetRepository;

    /**
     * @var AnalysisUserRepository
     */
    private $analysisUserRepository;

    /**
     * @var AnalysisTopicRepository
     */
    private $analysisTopicRepository;

    /**
     * ResultsAnalyser constructor.
     *
     * @param TweetRepository $tweetRepository
     * @param AnalysisUserRepository $analysisUserRepository
     * @param AnalysisTopicRepository $analysisTopicRepository
     */
    public function __construct(
        TweetRepository $tweetRepository,
        AnalysisUserRepository $analysisUserRepository,
        AnalysisTopicRepository $analysisTopicRepository
    ) {
        $this->tweetRepository = $tweetRepository;
        $this->analysisUserRepository = $analysisUserRepository;
        $this->analysisTopicRepository = $analysisTopicRepository;
    }

    /**
     * @param string $screenName
     *
     * @return ResultsObject
     */
    public function getResultsForUser(string $screenName): ResultsObject
    {
        /**
         * @var AnalysisUser $user
         */
        $user = $this->analysisUserRepository->findOneBy(['screen_name' => $screenName]);
        $positiveTweets = $this
            ->tweetRepository
            ->getNumberOfTweetsForUserIdWithSentiment($user->getId(), 'positive');
        $negativeTweets = $this
            ->tweetRepository
            ->getNumberOfTweetsForUserIdWithSentiment($user->getId(), 'negative');

        return new ResultsObject($user->getTweets(), $user, $positiveTweets, $negativeTweets);
    }

    /**
     * @param string $topic
     *
     * @return ResultsObject
     */
    public function getResultsForTopic(string $topic): ResultsObject
    {
        /**
         * @var AnalysisTopic $topic
         */
        $topic = $this->analysisTopicRepository->findOneBy(['term' => $topic]);
        $positiveTweets = $this
            ->tweetRepository
            ->getNumberOfTweetsForTopicIdWithSentiment($topic->getId(), 'positive');
        $negativeTweets = $this
            ->tweetRepository
            ->getNumberOfTweetsForTopicIdWithSentiment($topic->getId(), 'negative');

        return new ResultsObject($topic->getTweets(), $topic, $positiveTweets, $negativeTweets);
    }

    /**
     * @param int $userId
     * @param int $latestTweetId
     *
     * @return ResultsObject
     */
    public function getNewTweetsForUser(int $userId, int $latestTweetId): ResultsObject
    {
        /**
         * @var AnalysisUser $user
         */
        $user = $this->analysisUserRepository->find($userId);
        $tweets = $this->tweetRepository->findAllTweetsForUserIdWithTweetIdGreaterThan($userId, $latestTweetId);
        $positiveTweets = $this
            ->tweetRepository
            ->getNumberOfTweetsForUserIdWithSentiment($user->getId(), 'positive');
        $negativeTweets = $this
            ->tweetRepository
            ->getNumberOfTweetsForUserIdWithSentiment($user->getId(), 'negative');

        return new ResultsObject($tweets, $user, $negativeTweets, $positiveTweets);
    }

    /**
     * @param int $topicId
     * @param int $latestTweetId
     *
     * @return ResultsObject
     */
    public function getNewTweetsForTopic(int $topicId, int $latestTweetId): ResultsObject
    {
        /**
         * @var AnalysisTopic $topic
         */
        $topic = $this->analysisTopicRepository->find($topicId);
        $tweets = $this->tweetRepository->findAllTweetsForTopicIdWithTweetIdGreaterThan($topicId, $latestTweetId);
        $positiveTweets = $this
            ->tweetRepository
            ->getNumberOfTweetsForTopicIdWithSentiment($topic->getId(), 'positive');
        $negativeTweets = $this
            ->tweetRepository
            ->getNumberOfTweetsForTopicIdWithSentiment($topic->getId(), 'negative');

        return new ResultsObject($tweets, $topic, $negativeTweets, $positiveTweets);
    }
}
