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
use AppBundle\Service\ResultsAnalyser;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ResultsControllerTest extends WebTestCase
{
    public function testTopicResults()
    {
        $client = static::createClient();
        $tweet = $this->getMockBuilder(Tweet::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getAuthorScreenName', 'getText'])
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

        $tweets = new ArrayCollection([$tweet]);
        $term = $this->getMockBuilder(AnalysisEntityInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultsObject = new ResultsObject($tweets, $term, 100, 100);

        $mockResultsAnalyser = $this->getMockBuilder(ResultsAnalyser::class)
            ->disableOriginalConstructor()
            ->setMethods(['getResultsForTopic'])
            ->getMock();
        $mockResultsAnalyser->expects($this->once())->method('getResultsForTopic')
            ->willReturn($resultsObject);

        $client->getContainer()->set('app.results_analyser', $mockResultsAnalyser);

        $crawler = $client->request('GET', '/topic/test');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testUserResults()
    {
        $client = static::createClient();
        $tweet = $this->getMockBuilder(Tweet::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getAuthorScreenName', 'getText'])
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

        $tweets = new ArrayCollection([$tweet]);
        $term = $this->getMockBuilder(AnalysisEntityInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultsObject = new ResultsObject($tweets, $term, 100, 100);

        $mockResultsAnalyser = $this->getMockBuilder(ResultsAnalyser::class)
            ->disableOriginalConstructor()
            ->setMethods(['getResultsForUser'])
            ->getMock();
        $mockResultsAnalyser->expects($this->once())->method('getResultsForUser')
            ->willReturn($resultsObject);

        $client->getContainer()->set('app.results_analyser', $mockResultsAnalyser);

        $crawler = $client->request('GET', '/user/test');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testGetNewTweetsForTopic()
    {
        $client = static::createClient();
        $tweet = $this->getMockBuilder(Tweet::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getAuthorScreenName', 'getText'])
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

        $tweets = new ArrayCollection([$tweet]);
        $term = $this->getMockBuilder(AnalysisEntityInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultsObject = new ResultsObject($tweets, $term, 100, 100);

        $mockResultsAnalyser = $this->getMockBuilder(ResultsAnalyser::class)
            ->disableOriginalConstructor()
            ->setMethods(['getNewTweetsForTopic'])
            ->getMock();
        $mockResultsAnalyser->expects($this->once())->method('getNewTweetsForTopic')
            ->willReturn($resultsObject);

        $client->getContainer()->set('app.results_analyser', $mockResultsAnalyser);

        $crawler = $client->request('GET', '/refreshTweetList', [
            'term_type' => 'topic',
            'term_id' => 1,
            'latest_tweet_in_list' => 1,
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testGetNewTweetsForUser()
    {
        $client = static::createClient();
        $tweet = $this->getMockBuilder(Tweet::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getAuthorScreenName', 'getText'])
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

        $tweets = new ArrayCollection([$tweet]);
        $term = $this->getMockBuilder(AnalysisEntityInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultsObject = new ResultsObject($tweets, $term, 100, 100);

        $mockResultsAnalyser = $this->getMockBuilder(ResultsAnalyser::class)
            ->disableOriginalConstructor()
            ->setMethods(['getNewTweetsForUser'])
            ->getMock();
        $mockResultsAnalyser->expects($this->once())->method('getNewTweetsForUser')
            ->willReturn($resultsObject);

        $client->getContainer()->set('app.results_analyser', $mockResultsAnalyser);

        $crawler = $client->request('GET', '/refreshTweetList', [
            'term_type' => 'user',
            'term_id' => 1,
            'latest_tweet_in_list' => 1,
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
