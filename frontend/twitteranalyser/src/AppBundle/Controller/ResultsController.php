<?php

/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles the page showing the results of the analysis of a term.
 *
 * @author Mathieu Hendey <mhendey01@qub.ac.uk>
 */
class ResultsController extends Controller
{
    /**
     * @Route("/topic/{term}", name="topic_results")
     * @Template("default/results.html.twig")
     *
     * @param string $term
     *
     * @return array
     */
    public function topicResultsAction(string $term)
    {
        $resultsAnalyser = $this->get('app.results_analyser');
        $results = $resultsAnalyser->getResultsForTopic($term);

        return [
            'tweets' => $results->getTweets(),
            'term' => $results->getTerm(),
            'positiveTweets' => $results->getPositiveTweets(),
            'negativeTweets' => $results->getNegativeTweets(),
        ];
    }

    /**
     * @Route("/hashtag/{term}", name="hashtag_results")
     * @Template("default/results.html.twig")
     *
     * @param string $term
     *
     * @return array
     */
    public function hashtagResultsAction(string $term)
    {
        $resultsAnalyser = $this->get('app.results_analyser');
        $results = $resultsAnalyser->getResultsForHashtag($term);

        return [
            'tweets' => $results->getTweets(),
            'term' => $results->getTerm(),
            'positiveTweets' => $results->getPositiveTweets(),
            'negativeTweets' => $results->getNegativeTweets(),
        ];
    }

    /**
     * @Route("/user/{term}", name="user_results")
     * @Template("default/results.html.twig")
     *
     * @param string $term
     *
     * @return array
     */
    public function userResultsAction(string $term)
    {
        $resultsAnalyser = $this->get('app.results_analyser');
        $results = $resultsAnalyser->getResultsForUser($term);

        return [
            'tweets' => $results->getTweets(),
            'term' => $results->getTerm(),
            'positiveTweets' => $results->getPositiveTweets(),
            'negativeTweets' => $results->getNegativeTweets(),
        ];
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

        if ($request->get('term_type') == 'topic') {
            $results = $resultsAnalyser
                ->getNewTweetsForTopic($request->get('term_id'), $request->get('latest_tweet_in_list'));
        } else {
            $results = $resultsAnalyser
                ->getNewTweetsForUser($request->get('term_id'), $request->get('latest_tweet_in_list'));
        }

        $newTweetsRendered = $this->renderView(
            'default/tweet_list.html.twig',
                [
                    'tweets' => $results->getTweets(),
                ]
        );

        $data = [
            'view' => $newTweetsRendered,
            'positiveTweets' => $results->getPositiveTweets(),
            'negativeTweets' => $results->getNegativeTweets(),
        ];
        $response = new JsonResponse($data);

        return $response;
    }
}
