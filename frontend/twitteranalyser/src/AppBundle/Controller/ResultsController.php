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
     * @Template("default/results.html.twig")
     *
     * @param string $term
     *
     * @return array
     */
    public function topicResultsAction($term)
    {
        /**
         * @var TweetRepository $tweetRepository
         */
        $tweetRepository = $this->getDoctrine()->getRepository(Tweet::class);
        $topic = $this->getDoctrine()->getRepository(AnalysisTopic::class)->findOneBy(['term' => $term]);
        $positiveTweets = $tweetRepository->getNumberOfTweetsForTopicIdWithSentiment($topic->getId(), 'positive');
        $negativeTweets = $tweetRepository->getNumberOfTweetsForTopicIdWithSentiment($topic->getId(), 'negative');

        return [
            'tweets' => $topic->getTweets(),
            'term' => $topic,
            'positiveTweets' => $positiveTweets,
            'negativeTweets' => $negativeTweets,
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
    public function userResultsAction($term)
    {
        /**
         * @var TweetRepository $tweetRepository
         */
        $tweetRepository = $this->getDoctrine()->getRepository(Tweet::class);
        $user = $this->getDoctrine()->getRepository(AnalysisUser::class)->findOneBy(['screenName' => $term]);
        $positiveTweets = $tweetRepository->getNumberOfTweetsForUserIdWithSentiment($user->getId(), 'positive');
        $negativeTweets = $tweetRepository->getNumberOfTweetsForUserIdWithSentiment($user->getId(), 'negative');

        return [
            'tweets' => $user->getTweets(),
            'term' => $user,
            'positiveTweets' => $positiveTweets,
            'negativeTweets' => $negativeTweets,
        ];
    }

    /**
     * @Route("/refreshTweetList", name="refreshTweetList")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getNewTweetsAction(Request $request): JsonResponse
    {
        /**
         * @var TweetRepository $tweetRepository
         */
        $tweetRepository = $this->getDoctrine()->getRepository(Tweet::class);

        if ($request->get('term_type') == 'topic') {
            $term = $this
                ->getDoctrine()
                ->getRepository(AnalysisTopic::class)
                ->find($request->get('term_id'));
            $tweets = $tweetRepository->findAllTweetsForTopicIdWithTweetIdGreaterThan(
                $request->get('term_id'), $request->get('latest_tweet_in_list')
            );
            $positiveTweets = $tweetRepository
                ->getNumberOfTweetsForTopicIdWithSentiment($request->get('term_id'), 'positive');
            $negativeTweets = $tweetRepository
                ->getNumberOfTweetsForTopicIdWithSentiment($request->get('term_id'), 'negative');
        } else {
            $term = $this
                ->getDoctrine()
                ->getRepository(AnalysisUser::class)
                ->find($request->get('term_id'));
            $tweets = $tweetRepository->findAllTweetsForUserIdWithTweetIdGreaterThan(
                $request->get('term_id'), $request->get('latest_tweet_in_list')
            );
            $positiveTweets = $tweetRepository
                ->getNumberOfTweetsForUserIdWithSentiment($request->get('term_id'), 'positive');
            $negativeTweets = $tweetRepository
                ->getNumberOfTweetsForUserIdWithSentiment($request->get('term_id'), 'negative');
        }

        $newTweetsRendered = $this->renderView(
            'default/tweet_list.html.twig',
                [
                    'tweets' => $tweets,
                    'term' => $term,
                    'positiveTweets' => $positiveTweets,
                    'negativeTweets' => $negativeTweets,
                ]
        );

        $response = new JsonResponse();
        $data = [
            'view' => $newTweetsRendered,
            'positiveTweets' => $positiveTweets,
            'negativeTweets' => $negativeTweets,
        ];
        $response->setData($data);

        return $response;
    }
}
