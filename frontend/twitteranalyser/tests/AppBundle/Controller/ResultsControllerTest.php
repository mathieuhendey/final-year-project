<?php
/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7.1
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\AnalysisEntityInterface;
use AppBundle\Entity\Tweet;
use AppBundle\Model\ResultsObject;
use AppBundle\Service\CurrentAnalysesChecker;
use AppBundle\Service\ResultsAnalyser;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ResultsControllerTest extends WebTestCase
{
    /**
     * @var ResultsObject
     */
    private $resultsObject;

    /**
     * @var Client
     */
    private $client;

    protected function setUp()
    {
        $tweet = $this->getMockBuilder(Tweet::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getAuthorScreenName', 'getText', 'getCreatedOn'])
            ->getMock();
        $tweet->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        $tweet->expects($this->once())
            ->method('getAuthorScreenName')
            ->willReturn('test');
        $tweet->expects($this->once())
            ->method('getText')
            ->willReturn('test text');
        $tweet->expects($this->once())
            ->method('getCreatedOn')
            ->willReturn(new \DateTime());

        $tweets = new ArrayCollection([$tweet]);

        /**
         * @var AnalysisEntityInterface|\PHPUnit_Framework_MockObject_MockObject $term
         */
        $term = $this->getMockBuilder(AnalysisEntityInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultsObject = new ResultsObject($tweets, $term, 100, 100);
        $this->client = static::createClient();
    }

    public function testTopicResults()
    {
        $mockResultsAnalyser = $this->getMockBuilder(ResultsAnalyser::class)
            ->disableOriginalConstructor()
            ->setMethods(['getResultsForTopic'])
            ->getMock();
        $mockResultsAnalyser->expects($this->once())->method('getResultsForTopic')
            ->willReturn($this->resultsObject);

        $mockCurrentAnalysesChecker = $this->getMockBuilder(CurrentAnalysesChecker::class)
            ->disableOriginalConstructor()
            ->setMethods(['checkIfAnalysisIsRunning'])
            ->getMock();
        $mockCurrentAnalysesChecker->expects($this->once())->method('checkIfAnalysisIsRunning')
            ->willReturn(true);

        $this->client->getContainer()->set('app.results_analyser', $mockResultsAnalyser);
        $this->client->getContainer()->set('app.current_analyses_checker', $mockCurrentAnalysesChecker);

        $this->client->request('GET', '/topic/test');

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testUserResults()
    {
        $mockResultsAnalyser = $this->getMockBuilder(ResultsAnalyser::class)
            ->disableOriginalConstructor()
            ->setMethods(['getResultsForUser'])
            ->getMock();
        $mockResultsAnalyser->expects($this->once())->method('getResultsForUser')
            ->willReturn($this->resultsObject);

        $mockCurrentAnalysesChecker = $this->getMockBuilder(CurrentAnalysesChecker::class)
            ->disableOriginalConstructor()
            ->setMethods(['checkIfAnalysisIsRunning'])
            ->getMock();
        $mockCurrentAnalysesChecker->expects($this->once())->method('checkIfAnalysisIsRunning')
            ->willReturn(true);

        $this->client->getContainer()->set('app.results_analyser', $mockResultsAnalyser);
        $this->client->getContainer()->set('app.current_analyses_checker', $mockCurrentAnalysesChecker);

        $this->client->request('GET', '/user/test');

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testGetNewTweetsForTopic()
    {
        $mockResultsAnalyser = $this->getMockBuilder(ResultsAnalyser::class)
            ->disableOriginalConstructor()
            ->setMethods(['getNewTweetsForTopic'])
            ->getMock();
        $mockResultsAnalyser->expects($this->once())->method('getNewTweetsForTopic')
            ->willReturn($this->resultsObject);

        $mockCurrentAnalysesChecker = $this->getMockBuilder(CurrentAnalysesChecker::class)
            ->disableOriginalConstructor()
            ->setMethods(['checkIfAnalysisIsRunningWithIdAndType'])
            ->getMock();
        $mockCurrentAnalysesChecker->expects($this->once())->method('checkIfAnalysisIsRunningWithIdAndType')
            ->willReturn(true);

        $this->client->getContainer()->set('app.results_analyser', $mockResultsAnalyser);
        $this->client->getContainer()->set('app.current_analyses_checker', $mockCurrentAnalysesChecker);

        $this->client->request('GET', '/refreshTweetList', [
            'term_type' => 'topic',
            'term_id' => 1,
            'latest_tweet_in_list' => 1,
        ]);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testGetNewTweetsForUser()
    {
        $mockResultsAnalyser = $this->getMockBuilder(ResultsAnalyser::class)
            ->disableOriginalConstructor()
            ->setMethods(['getNewTweetsForUser'])
            ->getMock();
        $mockResultsAnalyser->expects($this->once())->method('getNewTweetsForUser')
            ->willReturn($this->resultsObject);

        $mockCurrentAnalysesChecker = $this->getMockBuilder(CurrentAnalysesChecker::class)
            ->disableOriginalConstructor()
            ->setMethods(['checkIfAnalysisIsRunningWithIdAndType'])
            ->getMock();
        $mockCurrentAnalysesChecker->expects($this->once())->method('checkIfAnalysisIsRunningWithIdAndType')
            ->willReturn(true);

        $this->client->getContainer()->set('app.results_analyser', $mockResultsAnalyser);
        $this->client->getContainer()->set('app.current_analyses_checker', $mockCurrentAnalysesChecker);

        $this->client->request('GET', '/refreshTweetList', [
            'term_type' => 'user',
            'term_id' => 1,
            'latest_tweet_in_list' => 1,
        ]);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
}
