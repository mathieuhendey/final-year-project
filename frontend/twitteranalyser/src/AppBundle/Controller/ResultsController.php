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
use AppBundle\Entity\Tweet;
use AppBundle\Repository\TweetRepository;
use AppBundle\Service\AnalysisGetter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
     * @Route("/{type}/{term}", name="results")
     * @Template("default/results.html.twig")
     *
     * @param string $type
     * @param string $term
     *
     * @return array
     */
    public function resultsAction(string $type, string $term): array
    {
        if ($type == AnalysisGetter::TYPE_PARAM_USER_VALUE) {
            $user = $this->getDoctrine()->getRepository(AnalysisUser::class)->findOneBy(['term' => $term]);
            return ['tweets' => $user->getTweets(), 'term' => $user];
        } elseif ($type == AnalysisGetter::TYPE_PARAM_TOPIC_VALUE) {
            $topic = $this->getDoctrine()->getRepository(AnalysisTopic::class)->findOneBy(['term' => $term]);
            return ['tweets' => $topic->getTweets(), 'term' => $topic];
        }

        return [];
    }

    /**
     * @Route("/topic/{term}", name="topic_results")
     * @Template("default/results.html.twig")
     *
     * @param string $term
     *
     * @return array
     */
    public function topicResultsAction($term)
    {
        $topic = $this->getDoctrine()->getRepository(AnalysisTopic::class)->findOneBy(['term' => $term]);
        return ['tweets' => $topic->getTweets(), 'term' => $topic];
    }

    /**
     * @Route("/user/{term}", name="user_results")
     * @Template("default/results.html.twig")
     *
     * @param string $term
     *
     * @return array
     */
    public function userResultsAction($term)
    {
        $user = $this->getDoctrine()->getRepository(AnalysisUser::class)->findOneBy(['term' => $term]);
        return ['tweets' => $user->getTweets(), 'term' => $user];
    }

    /**
     * @Route("/refreshTweetList", name="refreshTweetList")
     *
     * @param Request $request
     * @return string
     */
    public function getNewTweetsAction(Request $request)
    {
        /**
         * @var TweetRepository $tweetRepository
         */
        $tweetRepository = $this->getDoctrine()->getRepository(Tweet::class);

        if ($request->get('term_type') == 'topic') {
            $tweets = $tweetRepository->findAllTweetsForTopicIdWithTweetIdGreaterThan(
                $request->get('term_id'), $request->get('latest_tweet_in_list')
            );
        } else {
            $tweets = $tweetRepository->findAllTweetsForUserIdWithTweetIdGreaterThan(
                $request->get('term_id'), $request->get('latest_tweet_in_list')
            );
        }

        return new Response($this->renderView('default/tweet_list.html.twig', ['tweets' => $tweets]));
    }
}
