<?php

/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Controller;

use AppBundle\Service\TwitterCommunication\TweetFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * @var TweetFetcherInterface
     */
    private $tweetFetcher;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * DefaultController constructor.
     *
     * @param TweetFetcherInterface $tweetFetcher
     * @param EngineInterface       $templating
     */
    public function __construct(
        TweetFetcherInterface $tweetFetcher,
        EngineInterface $templating
    ) {
        $this->tweetFetcher = $tweetFetcher;
        $this->templating = $templating;
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

        return ['account_information' => $this->tweetFetcher->getAccountDetails()];
    }
}
