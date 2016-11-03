<?php

/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Service\TwitterCommunication;

use AppBundle\Entity\Tweet;
use AppBundle\Entity\TwitterUser;

/**
 * Interface for classes that fetch Tweets from Twitter.
 *
 * @author Mathieu Hendey <mhendey01@qub.ac.uk>
 */
interface TweetFetcherInterface
{
    /**
     * Returns the Twitter URL which the user should be directed to in order
     * to give the app read permissions.
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getAuthorisationURL(): string;

    /**
     * Given a user, return their Tweets.
     *
     * @param TwitterUser $twitterUser
     *
     * @return Tweet[]
     */
    public function getTweetsFromUser(TwitterUser $twitterUser): array;

    /**
     * Given a Tweet, find Likes of that Tweet.
     *
     * @param Tweet $tweet
     *
     * @return Tweet[]
     */
    public function getResponsesToTweet(Tweet $tweet): array;

    /**
     * Given a Tweet, find Retweets of that Tweet.
     *
     * @param Tweet $tweet
     *
     * @return array
     */
    public function getLikesForTweet(Tweet $tweet): array;

    /**
     * Given a Tweet, find Retweets of that Tweet.
     *
     * @param Tweet $tweet
     *
     * @return array
     */
    public function getRetweetsForTweet(Tweet $tweet): array;

    /**
     * Get the currently signed-in user's Twitter details.
     *
     * This includes things like their real name, their user name, a summary
     * of their recent Tweets etc.
     *
     * @return array|object
     */
    public function getAccountDetails();

    /**
     * Get the long-lived access token that lets us perform actions on Twitter
     * on the user's behalf.
     *
     * @param string $oAuthVerifier
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getAccessToken(string $oAuthVerifier): array;

    /**
     * Set the OAuthToken that confirms that the user has given us permission
     * to access Twitter on their behalf.
     *
     * The token may be a temporary token (used for initial authorisation), or
     * it may be a permanent one (used for any requests after the user has
     * given their initial authorisation.
     *
     * @param string $oAuthToken
     * @param string $oAuthTokenSecret
     */
    public function setOAuthToken(string $oAuthToken, string $oAuthTokenSecret);
}
