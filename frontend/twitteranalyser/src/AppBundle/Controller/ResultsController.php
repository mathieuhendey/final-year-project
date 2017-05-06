<?php

/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles the page showing the results of the analysis of a term.
 *
 * @author Mathieu Hendey <mhendey01@qub.ac.uk>
 */
class ResultsController extends Controller
{
    /**
     * @Route("/topic/{term}", name="topic_results")
     *
     * @param string $term
     *
     * @return Response
     */
    public function topicResultsAction(string $term): Response
    {
        $currentAnalysesChecker = $this->get('app.current_analyses_checker');

        $resultsAnalyser = $this->get('app.results_analyser');
        $results = $resultsAnalyser->getResultsForTopic($term);
        $currentlyAnalysing = $currentAnalysesChecker->checkIfAnalysisIsRunning($results);

        return $this->render('default/results.html.twig', [
            'tweets' => $results->getTweets(),
            'term' => $results->getTerm(),
            'positiveTweets' => $results->getPositiveTweets(),
            'negativeTweets' => $results->getNegativeTweets(),
            'currentlyAnalysing' => $currentlyAnalysing,
        ]);
    }

    /**
     * @Route("/hashtag/{term}", name="hashtag_results")
     *
     * @param string $term
     *
     * @return Response
     */
    public function hashtagResultsAction(string $term): Response
    {
        $currentAnalysesChecker = $this->get('app.current_analyses_checker');

        $resultsAnalyser = $this->get('app.results_analyser');
        $results = $resultsAnalyser->getResultsForHashtag($term);
        $currentlyAnalysing = $currentAnalysesChecker->checkIfAnalysisIsRunning($results);

        return $this->render('default/results.html.twig', [
            'tweets' => $results->getTweets(),
            'term' => $results->getTerm(),
            'positiveTweets' => $results->getPositiveTweets(),
            'negativeTweets' => $results->getNegativeTweets(),
            'currentlyAnalysing' => $currentlyAnalysing,
        ]);
    }

    /**
     * @Route("/user/{term}", name="user_results")
     *
     * @param string $term
     *
     * @return Response
     */
    public function userResultsAction(string $term): Response
    {
        $currentAnalysesChecker = $this->get('app.current_analyses_checker');

        $resultsAnalyser = $this->get('app.results_analyser');
        $results = $resultsAnalyser->getResultsForUser($term);
        $currentlyAnalysing = $currentAnalysesChecker->checkIfAnalysisIsRunning($results);

        return $this->render('default/results.html.twig', [
            'tweets' => $results->getTweets(),
            'term' => $results->getTerm(),
            'positiveTweets' => $results->getPositiveTweets(),
            'negativeTweets' => $results->getNegativeTweets(),
            'currentlyAnalysing' => $currentlyAnalysing,
        ]);
    }

    /**
     * @Route("/refreshTweetList", name="refreshTweetList")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getNewTweetsAction(Request $request): JsonResponse
    {
        $resultsAnalyser = $this->get('app.results_analyser');
        $currentAnalysesChecker = $this->get('app.current_analyses_checker');

        $termType = $request->get('term_type');
        $termId = $request->get('term_id');
        $latestTweetInList = $request->get('latest_tweet_in_list');

        if (!$currentAnalysesChecker->checkIfAnalysisIsRunningWithIdAndType($termId, $termType)) {
            $data = [
                'analysing' => false,
                'view' => '',
                'positiveTweets' => [],
                'negativeTweets' => [],
            ];

            $response = new JsonResponse($data);

            return $response;
        }

        $results = $termType == 'topic' || $termType == 'hashtag'
            ? $resultsAnalyser->getNewTweetsForTopic($termId, $latestTweetInList)
            : $resultsAnalyser->getNewTweetsForUser($termId, $latestTweetInList);

        $newTweetsRendered = $this->renderView(
            'default/tweet_list.html.twig',
                [
                    'tweets' => $results->getTweets(),
                ]
        );

        $data = [
            'analysing' => true,
            'view' => $newTweetsRendered,
            'positiveTweets' => $results->getPositiveTweets(),
            'negativeTweets' => $results->getNegativeTweets(),
        ];
        $response = new JsonResponse($data);

        return $response;
    }
}
