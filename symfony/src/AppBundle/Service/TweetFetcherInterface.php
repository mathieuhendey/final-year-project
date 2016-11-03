<?php

namespace AppBundle\Service;

use AppBundle\Entity\Tweet;
use AppBundle\Entity\TwitterUser;

/**
 * Interface for classes that fetch Tweets from Twitter.
 */
interface TweetFetcherInterface
{
    public function getTweetsFromUser(TwitterUser $twitterUser): array;

    public function getResponsesToTweet(Tweet $tweet): array;

    public function getLikesForTweet(Tweet $tweet): array;

    public function getRetweetsForTweet(Tweet $tweet): array;
}
