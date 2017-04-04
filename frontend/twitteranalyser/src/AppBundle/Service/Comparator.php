<?php
/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7.1
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Service;

use AppBundle\Repository\AnalysisTopicRepository;
use AppBundle\Repository\AnalysisUserRepository;
use AppBundle\Repository\TweetRepository;

class Comparator
{
    /**
     * @var TweetRepository
     */
    private $tweetRepository;

    /**
     * @var AnalysisUserRepository
     */
    private $analysisUserRepository;

    /**
     * @var AnalysisTopicRepository
     */
    private $analysisTopicRepository;

    public function __construct(TweetRepository $tweetRepository, AnalysisUserRepository $analysisUserRepository, AnalysisTopicRepository $analysisTopicRepository)
    {
        $this->tweetRepository = $tweetRepository;
        $this->analysisUserRepository = $analysisTopicRepository;
        $this->analysisTopicRepository = $analysisTopicRepository;
    }
}
