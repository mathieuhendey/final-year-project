<?php

namespace AppBundle\Controller;

use AppBundle\Service\TweetFetcher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class DefaultController.
 *
 * @Route(service="app.default_controller")
 */
class DefaultController extends Controller
{
    /**
     * @var TweetFetcher
     */
    private $tweetFetcher;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Session
     */
    private $session;

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
     */
    public function indexAction(Request $request): array
    {
        var_dump($this->tweetFetcher->getAccountDetails());

        return [];
    }
}
