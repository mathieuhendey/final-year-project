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
use AppBundle\Service\AnalysisGetter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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

            return ['tweets' => $user->getTweets(), 'term' => $user->getScreenName()];
        } elseif ($type == AnalysisGetter::TYPE_PARAM_TOPIC_VALUE) {
            $topic = $this->getDoctrine()->getRepository(AnalysisTopic::class)->findOneBy(['term' => $term]);

            return ['tweets' => $topic->getTweets(), 'term' => $topic->getTerm()];
        }

        return [];
    }

    /**
     * @Route("/refresh", name="refresh")
     *
     * @return string
     */
    public function getNewDataAction()
    {
        $tweets = $this->getDoctrine()->getRepository(Tweet::class)->findAll();

        return $this->renderView('default/tweet_list.html.twig', ['tweets' => $tweets]);
    }
}
