<?php

/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Controller;

use AppBundle\Entity\AnalysisTopic;
use AppBundle\Entity\AnalysisUser;
use AppBundle\Service\AnalysisGetter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles the home page of the application.
 *
 * @author Mathieu Hendey <mhendey01@qub.ac.uk>
 */
class HomePageController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template("default/index.html.twig")
     *
     * @return array
     */
    public function indexAction(): array
    {
        return ['test' => 'todo'];
    }

    /**
     * @Route("/analyse", name="analyse")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function beginAnalysisAction(Request $request)
    {
        $analysisGetter = $this->get('app.analysis_getter');
        $result = $analysisGetter->startAnalysis($request);

        if ($result === false) {
            // TODO: Handle this case
        } elseif ($result->isTopic()) {
            $topic = $this->getDoctrine()->getRepository(AnalysisTopic::class)->find($result->getId());

            return $this->redirectToRoute(
                'results',
                ['type' => AnalysisGetter::TYPE_PARAM_TOPIC_VALUE, 'term' => $topic->getTerm()]
            );
        } else {
            $user = $this->getDoctrine()->getRepository(AnalysisUser::class)->find($result->getId());

            return $this->redirectToRoute(
                'results',
                ['type' => AnalysisGetter::TYPE_PARAM_USER_VALUE, 'term' => $user->getTerm()]
            );
        }
    }
}
