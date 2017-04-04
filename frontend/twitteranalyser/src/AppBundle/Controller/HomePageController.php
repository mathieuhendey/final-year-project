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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

/**
 * Handles the home page of the application.
 *
 * @author Mathieu Hendey <mhendey01@qub.ac.uk>
 */
class HomePageController extends Controller
{
    /**
     * @Route("/", name="homepage")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $searchTerm = $request->get('search_term', '');
        $shouldReanalyse = $request->get('should_reanalyse', false);

        return $this->render('default/index.html.twig', [
            'searchTerm' => $searchTerm,
            'shouldReanalyse' => $shouldReanalyse,
        ]);
    }

    /**
     * @Route("/analyse", name="analyse")
     *
     * @param Request $request
     *
     * @throws TooManyRequestsHttpException
     *
     * @return RedirectResponse
     */
    public function beginAnalysisAction(Request $request): RedirectResponse
    {
        $analysisGetter = $this->get('app.analysis_getter');
        $analysisTermValidator = $this->get('app.analysis_term_validator');

        $validationErrors = $analysisTermValidator->validate($request->get('term', ''));

        if (!empty($validationErrors)) {
            foreach ($validationErrors as $validationError) {
                $this->addFlash('error', $validationError);
            }

            return $this->redirectToRoute('homepage');
        }

        $result = $analysisGetter->startAnalysis($request);

        if ($result->isRateLimited()) {
            throw new TooManyRequestsHttpException($result->getTimeLeftOnStream(), 'Rate limited for '.$result->getTimeLeftOnStream().' seconds!');
        } elseif ($result->isTopic() && !$result->isHashtag()) {
            $topic = $this->getDoctrine()->getRepository(AnalysisTopic::class)->find($result->getId());

            return $this->redirectToRoute(
                'topic_results',
                    [
                        'term' => $topic->getTerm(),
                    ]
            );
        } elseif ($result->isHashtag()) {
            $topic = $this->getDoctrine()->getRepository(AnalysisTopic::class)->find($result->getId());

            return $this->redirectToRoute(
                'hashtag_results',
                [
                    'term' => $topic->getTerm(),
                ]
            );
        } else {
            $user = $this->getDoctrine()->getRepository(AnalysisUser::class)->find($result->getId());

            return $this->redirectToRoute(
                'user_results',
                [
                    'term' => $user->getScreenName(),
                ]
            );
        }
    }
}
