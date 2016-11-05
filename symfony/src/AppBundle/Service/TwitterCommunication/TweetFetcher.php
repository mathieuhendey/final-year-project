<?php

/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Service\TwitterCommunication;

use Abraham\TwitterOAuth\TwitterOAuth;
use AppBundle\Entity\Tweet;
use AppBundle\Entity\TwitterUser;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Implements the TweetFetcherInterface, allowing me to strip out TwitterOAuth
 * and replace it if I find something better.
 *
 * It abstracts the methods provided by TwitterOAuth to reverse the app's
 * dependency on it.
 *
 * @author Mathieu Hendey <mhendey01@qub.ac.uk>
 */
class TweetFetcher implements TweetFetcherInterface
{
    /**
     * The callback URL that Twitter will redirect the user to after they've
     * given permission for the app to access Twitter on their behalf.
     *
     * @todo load this value from config
     *
     * @var string
     */
    const OAUTH_CALLBACK = 'http://localhost/app_dev.php/authorise';

    /**
     * Third party library that allows hitting generic endpoints on the Twitter
     * API.
     *
     * @var TwitterOAuth
     */
    private $twitterOAuth;

    /**
     * The session used to share data between this service and the rest of the
     * application. Used to store the user's access credentials.
     *
     * @var Session
     */
    private $session;

    /**
     * TweetFetcher constructor.
     *
     * @param TwitterOAuth $twitterOAuth
     * @param Session      $session
     */
    public function __construct(TwitterOAuth $twitterOAuth, Session $session)
    {
        $this->twitterOAuth = $twitterOAuth;
        $this->session = $session;

        /*
         * If we already have an access token stored in session, the current
         * user must already be authenticated.
         */
        if ($this->session->has('access_token')) {
            $accessToken = $this->session->get('access_token');
            $this->twitterOAuth->setOauthToken($accessToken['oauth_token'], $accessToken['oauth_token_secret']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorisationURL(): string
    {
        $requestToken = $this->twitterOAuth->oauth('oauth/request_token', ['oauth_callback' => self::OAUTH_CALLBACK]);
        $this->session->set('oauth_token', $requestToken['oauth_token']);
        $this->session->set('oauth_token_secret', $requestToken['oauth_token_secret']);

        $url = $this->twitterOAuth->url('oauth/authorize', ['oauth_token' => $requestToken['oauth_token']]);

        return $url;
    }

    /**
     * {@inheritdoc}
     *
     * @todo implement getTweetsFromUser() method
     */
    public function getTweetsFromUser(TwitterUser $twitterUser): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     *
     * @todo implement getResponsesToTweet() method
     */
    public function getResponsesToTweet(Tweet $tweet): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     *
     * @todo implement getLikesForTweet() method
     */
    public function getLikesForTweet(Tweet $tweet): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     *
     * @todo implement getRetweetsForTweet() method
     */
    public function getRetweetsForTweet(Tweet $tweet): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAccountDetails()
    {
        return $this->twitterOAuth->get('account/verify_credentials');
    }

    /**
     * {@inheritdoc}
     */
    public function setOAuthToken(string $oAuthToken, string $oAuthTokenSecret)
    {
        $this->twitterOAuth->setOauthToken($oAuthToken, $oAuthTokenSecret);

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessToken(string $oAuthVerifier): array
    {
        return $this->twitterOAuth->oauth('oauth/access_token', ['oauth_verifier' => $oAuthVerifier]);
    }
}
