<?php

namespace AppBundle\Service;

use Abraham\TwitterOAuth\TwitterOAuth;
use AppBundle\Entity\Tweet;
use AppBundle\Entity\TwitterUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;

class TweetFetcher extends Controller implements TweetFetcherInterface
{
    const OAUTH_CALLBACK = 'http://localhost/app_dev.php/authorise';

    /**
     * @var TwitterOAuth
     */
    private $twitterOAuth;

    /**
     * @var Session
     */
    private $session;

    public function __construct(
        TwitterOAuth $twitterOAuth,
        Session $session
    ) {
        $this->twitterOAuth = $twitterOAuth;
        $this->session = $session;

        if ($this->session->has('access_token')) {
            $accessToken = $this->session->get('access_token');
            $this->twitterOAuth->setOauthToken($accessToken['oauth_token'], $accessToken['oauth_token_secret']);
        }
    }

    public function getAuthorisationURL(): string
    {
        $requestToken = $this->twitterOAuth->oauth('oauth/request_token', ['oauth_callback' => self::OAUTH_CALLBACK]);
        $this->session->set('oauth_token', $requestToken['oauth_token']);
        $this->session->set('oauth_token_secret', $requestToken['oauth_token_secret']);

        $url = $this->twitterOAuth->url('oauth/authorize', ['oauth_token' => $requestToken['oauth_token']]);

        return $url;
    }

    public function getTweetsFromUser(TwitterUser $twitterUser): array
    {
        // TODO: Implement getTweetsFromUser() method.
    }

    public function getResponsesToTweet(Tweet $tweet): array
    {
        // TODO: Implement getResponsesToTweet() method.
    }

    public function getLikesForTweet(Tweet $tweet): array
    {
        // TODO: Implement getLikesForTweet() method.
    }

    public function getRetweetsForTweet(Tweet $tweet): array
    {
        // TODO: Implement getRetweetsForTweet() method.
    }

    public function getAccountDetails()
    {
        return $this->twitterOAuth->get('account/verify_credentials');
    }

    /**
     * @param string $oauthToken
     * @param string $oauthTokenSecret
     */
    public function setOauthToken($oauthToken, $oauthTokenSecret)
    {
        $this->twitterOAuth->setOauthToken($oauthToken, $oauthTokenSecret);
    }

    public function getAccessToken($oauthVerifier)
    {
        return $this->twitterOAuth->oauth('oauth/access_token', ['oauth_verifier' => $oauthVerifier]);
    }
}
