<?php

namespace AppBundle\Controller\TwitterAuthorisation;

use AppBundle\Service\TweetFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class TwitterAuthoriser.
 *
 * @Route(service="app.twitter_authoriser")
 */
class TwitterAuthoriser extends Controller
{
    /**
     * @var TweetFetcher
     */
    private $tweetFetcher;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * TwitterAuthoriser constructor.
     *
     * @param TweetFetcher    $tweetFetcher
     * @param Session         $session
     * @param RouterInterface $router
     */
    public function __construct(TweetFetcher $tweetFetcher, Session $session, RouterInterface $router)
    {
        $this->tweetFetcher = $tweetFetcher;
        $this->session = $session;
        $this->router = $router;
    }

    /**
     * @Route("/authorise_with_twitter", name="authorise_with_twitter")
     */
    public function authoriseOnTwitterAction(Request $request): RedirectResponse
    {
        $url = $this->tweetFetcher->getAuthorisationURL();
        $this->redirect($url);

        return $this->redirect($url);
    }

    /**
     * @Route("/authorise", name="complete_authorisation")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function completeAuthorisation(Request $request): RedirectResponse
    {
        $requestToken = [];
        $requestToken['oauth_token'] = $this->session->get('oauth_token');
        $requestToken['oauth_token_secret'] = $this->session->get('oauth_token_secret');

        $this->tweetFetcher->setOauthToken($requestToken['oauth_token'], $requestToken['oauth_token_secret']);

        $accessToken = $this->tweetFetcher->getAccessToken($request->get('oauth_verifier'));
        $this->session->set('access_token', $accessToken);

        return new RedirectResponse($this->router->generate('homepage'));
    }
}
