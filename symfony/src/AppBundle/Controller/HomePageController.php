<?php

/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @link https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Controller;

use AppBundle\Service\TwitterCommunication\TweetFetcher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * Handles the home page of the application.
 *
 * @author Mathieu Hendey <mhendey01@qub.ac.uk>
 *
 * @Route(service="app.home_page_controller")
 */
class HomePageController extends Controller
{
    /**
     * Service for communicating with the Twitter API.
     * 
     * @var TweetFetcher
     */
    private $tweetFetcher;

    /**
     * Symfony's templating engine. Converts Twig templates into HTML.
     * 
     * @var EngineInterface
     */
    private $templating;

    /**
     * Symfony's router. Generates URLs from route names and replaces any
     * provided parameter placeholders.
     * 
     * @var RouterInterface
     */
    private $router;

    /**
     * Session used for sharing data throughout the app. Used while I implement
     * persistence to the DB.
     *
     * @var Session
     */
    private $session;

    /**
     * DefaultController constructor.
     *
     * @param TweetFetcher    $tweetFetcher
     * @param EngineInterface $templating
     * @param RouterInterface $router
     * @param Session         $session
     */
    public function __construct(
        TweetFetcher $tweetFetcher,
        EngineInterface $templating,
        RouterInterface $router,
        Session $session
    ) {
        $this->tweetFetcher = $tweetFetcher;
        $this->templating = $templating;
        $this->router = $router;
        $this->session = $session;
    }

    /**
     * @Route("/", name="homepage")
     * @Template("default/index.html.twig")
     *
     * @return array
     */
    public function indexAction(): array
    {
        // For a POC just dump the authenticated user's details to the screen.
        var_dump($this->tweetFetcher->getAccountDetails());

        return [];
    }
}
