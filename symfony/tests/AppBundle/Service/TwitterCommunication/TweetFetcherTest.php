<?php
/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @link https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace Tests\AppBundle\Service\TwitterCommunication;

use Abraham\TwitterOAuth\TwitterOAuth;
use AppBundle\Service\TwitterCommunication\TweetFetcher;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class TweetFetcherTest.
 *
 * @author Mathieu Hendey <mhendey01@qub.ac.uk>
 */
class TweetFetcherTest extends \PHPUnit_Framework_TestCase
{
    const OAUTH_REQUEST_TOKEN_URL = 'oauth/request_token';
    const OAUTH_AUTHORIZE_URL = 'oauth/authorize';

    const TEST_REQUEST_TOKEN_OAUTH_TOKEN = 'testOAuthToken';
    const TEST_REQUEST_TOKEN_OAUTH_TOKEN_SECRET = 'testOAuthTokenSecret';

    const TWITTER_AUTHORISATION_URL = 'http://authorisation_url';
    const REQUEST_TOKEN = [
        'oauth_token' => 'testOAuthToken',
        'oauth_token_secret' => 'testOAuthTokenSecret',
    ];

    /**
     * @var TwitterOAuth|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $twitterOAuthMock;

    /**
     * @var Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionMock;

    protected function setUp()
    {
        $this->twitterOAuthMock = $this->createMock(TwitterOAuth::class);
        $this->sessionMock = $this->createMock(Session::class);
    }

    public function testGetAuthorisationUrl()
    {
        $tweetFetcher = $this->getTweetFetcher();

        $this->twitterOAuthMock->expects($this->once())
            ->method('oauth')
            ->with(self::OAUTH_REQUEST_TOKEN_URL, ['oauth_callback' => TweetFetcher::OAUTH_CALLBACK])
            ->willReturn(self::REQUEST_TOKEN);

        $this->sessionMock->expects($this->at(0))
            ->method('set')
            ->with('oauth_token', self::REQUEST_TOKEN['oauth_token']);

        $this->sessionMock->expects($this->at(1))
            ->method('set')
            ->with('oauth_token_secret', self::REQUEST_TOKEN['oauth_token_secret']);

        $this->twitterOAuthMock->expects($this->once())
            ->method('url')
            ->with(self::OAUTH_AUTHORIZE_URL, ['oauth_token' => self::REQUEST_TOKEN['oauth_token']])
            ->willReturn(self::TWITTER_AUTHORISATION_URL);

        $this->assertEquals(self::TWITTER_AUTHORISATION_URL, $tweetFetcher->getAuthorisationUrl());
    }

    /**
     * @return TweetFetcher
     */
    private function getTweetFetcher(): TweetFetcher
    {
        return new TweetFetcher($this->twitterOAuthMock, $this->sessionMock);
    }
}
