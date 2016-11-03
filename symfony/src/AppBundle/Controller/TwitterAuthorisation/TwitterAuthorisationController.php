<?php

/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Controller\TwitterAuthorisation;

use AppBundle\Service\TwitterCommunication\TweetFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Handles the authorisation flow. Triggered when the user clicks the
 * "authorise on Twitter" link on the home page, and also handles saving the
 * user's token and secret upon their being returned from Twitter.
 *
 * @author Mathieu Hendey <mhendey01@qub.ac.uk>
 *
 * @Route(service="app.twitter_authorisation")
 */
class TwitterAuthorisationController extends Controller
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
     * TwitterAuthorisation constructor.
     *
     * @param TweetFetcher    $tweetFetcher
     * @param Session         $session
     * @param RouterInterface $router
     */
    public function __construct(
        TweetFetcher $tweetFetcher,
        Session $session,
        RouterInterface $router
    ) {
        $this->tweetFetcher = $tweetFetcher;
        $this->session = $session;
        $this->router = $router;
    }

    /**
     * Handles the user clicking the "Authorise on Twitter" link.
     *
     * Generates the unique authorisation link and redirects them to Twitter.
     *
     * @Route("/authorise_with_twitter", name="authorise_with_twitter")
     *
     * @return RedirectResponse
     */
    public function authoriseOnTwitterAction(): RedirectResponse
    {
        $url = $this->tweetFetcher->getAuthorisationURL();
        $this->redirect($url);

        return $this->redirect($url);
    }

    /**
     * Handles the call back from Twitter. Pulls the long-lived credentials
     * from the response and saves them to the session.
     *
     * @Route("/authorise", name="complete_authorisation")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function completeAuthorisation(Request $request): RedirectResponse
    {
        $requestToken = [];
        $requestToken['oauth_token'] = $this->session->get('oauth_token');
        $requestToken['oauth_token_secret'] = $this->session->get('oauth_token_secret');

        if (is_null($request->get('oauth_token')) && $requestToken['oauth_token'] !== $requestToken['oauth_token']) {
            /*
             * The credentials we sent to Twitter should match the ones they
             * send back. If they don't match something has gone wrong.
             */
            throw new \Exception();
        }

        $this->tweetFetcher->setOAuthToken($requestToken['oauth_token'], $requestToken['oauth_token_secret']);

        $accessToken = $this->tweetFetcher->getAccessToken($request->get('oauth_verifier'));
        $this->session->set('access_token', $accessToken);

        return new RedirectResponse($this->router->generate('homepage'));
    }
}
