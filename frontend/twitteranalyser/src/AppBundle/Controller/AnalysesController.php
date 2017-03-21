<?php
/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7.1
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Controller;

use AppBundle\Entity\AnalysisTopic;
use AppBundle\Entity\AnalysisUser;
use AppBundle\Entity\Tweet;
use NumberFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class AnalysesController extends Controller
{
    /**
     * @Route("/analyses/topics", name="topic_analyses")
     * @Template("default/analyses.html.twig")
     *
     * @return array
     */
    public function topicAnalysesAction()
    {
        $spellerFormatter = new NumberFormatter('en-GB', NumberFormatter::SPELLOUT);
        $spellerFormatter->setTextAttribute(NumberFormatter::DEFAULT_RULESET, '%spellout-cardinal-verbose');

        $topicRepository = $this->getDoctrine()->getRepository(AnalysisTopic::class);
        $topics = $topicRepository->findAll();

        $tweetRepository = $this->getDoctrine()->getRepository(Tweet::class);
        $tweets = $tweetRepository->getTotalNumberOfTweetsForTopics();
        $tweetsSpelled = $spellerFormatter->format($tweets);

        $headerText = count($topics) !== 1
            ? ucfirst($spellerFormatter->format(count($topics))).' topics have been analysed'
            : ucfirst($spellerFormatter->format(count($topics))).' topic has been analysed';

        switch ($tweets) {
            case 0:
                $leadText = "That's <strong>$tweetsSpelled</strong> Tweets' worth :(";
                break;
            case 1:
                $leadText = "That's <strong>$tweetsSpelled</strong> Tweet's worth!";
                break;
            default:
                $leadText = "That's <strong>$tweetsSpelled</strong> Tweets' worth!";
        }

        return [
            'number' => count($topics),
            'headerText' => $headerText,
            'leadText' => $leadText,
            'items' => $topics,
        ];
    }

    /**
     * @Route("/analyses/users", name="user_analyses")
     * @Template("default/analyses.html.twig")
     *
     * @return array
     */
    public function userAnalysesAction()
    {
        $spellerFormatter = new NumberFormatter('en-GB', NumberFormatter::SPELLOUT);
        $spellerFormatter->setTextAttribute(NumberFormatter::DEFAULT_RULESET, '%spellout-cardinal-verbose');

        $userRepository = $this->getDoctrine()->getRepository(AnalysisUser::class);
        $users = $userRepository->findAll();

        $tweetRepository = $this->getDoctrine()->getRepository(Tweet::class);
        $tweets = $tweetRepository->getTotalNumberOfTweetsForUsers();
        $tweetsSpelled = $spellerFormatter->format($tweets);

        $headerText = count($users) !== 1
            ? ucfirst($spellerFormatter->format(count($users))).' users have been analysed'
            : ucfirst($spellerFormatter->format(count($users))).' user has been analysed';

        switch ($tweets) {
            case 0:
                $leadText = "That's <strong>$tweetsSpelled</strong> Tweets' worth :(";
                break;
            case 1:
                $leadText = "That's <strong>$tweetsSpelled</strong> Tweet's worth!";
                break;
            default:
                $leadText = "That's <strong>$tweetsSpelled</strong> Tweets' worth!";
        }

        return [
            'number' => count($users),
            'headerText' => $headerText,
            'leadText' => $leadText,
            'items' => $users,
        ];
    }
}
