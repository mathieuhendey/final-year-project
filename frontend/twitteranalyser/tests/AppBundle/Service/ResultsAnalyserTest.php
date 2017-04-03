<?php
/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace Tests\AppBundle\Service;

use AppBundle\Entity\AnalysisTopic;
use AppBundle\Entity\AnalysisUser;
use AppBundle\Model\ResultsObject;
use AppBundle\Repository\AnalysisTopicRepository;
use AppBundle\Repository\AnalysisUserRepository;
use AppBundle\Repository\TweetRepository;
use AppBundle\Service\ResultsAnalyser;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class ResultsAnalyserTest extends TestCase
{
    public function testGetResultsForUser()
    {
        $mockAnalysisUser = $this->getMockBuilder(AnalysisUser::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockAnalysisUser->expects($this->exactly(2))->method('getId')
            ->willReturn(1);

        $mockUserRepo = $this->getMockBuilder(AnalysisUserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockUserRepo->expects($this->once())->method('findOneBy')
            ->willReturn($mockAnalysisUser);

        $mockTopicRepo = $this->getMockBuilder(AnalysisTopicRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockTweetRepo = $this->getMockBuilder(TweetRepository::class)
            ->setMethods(['getNumberOfTweetsForUserIdWithSentiment'])
            ->disableOriginalConstructor()
            ->getMock();
        $mockTweetRepo->expects($this->exactly(2))->method('getNumberOfTweetsForUserIdWithSentiment')
            ->willReturn(20);

        $analyser = new ResultsAnalyser($mockTweetRepo, $mockUserRepo, $mockTopicRepo);

        $result = $analyser->getResultsForUser('test');

        $this->assertInstanceOf(ResultsObject::class, $result);
    }

    public function testGetResultsForTopic()
    {
        $mockAnalysisTopic = $this->getMockBuilder(AnalysisTopic::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockAnalysisTopic->expects($this->exactly(2))->method('getId')
            ->willReturn(1);

        $mockUserRepo = $this->getMockBuilder(AnalysisUserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockTopicRepo = $this->getMockBuilder(AnalysisTopicRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockTopicRepo->expects($this->once())->method('findOneBy')
            ->willReturn($mockAnalysisTopic);

        $mockTweetRepo = $this->getMockBuilder(TweetRepository::class)
            ->setMethods(['getNumberOfTweetsForTopicIdWithSentiment'])
            ->disableOriginalConstructor()
            ->getMock();
        $mockTweetRepo->expects($this->exactly(2))->method('getNumberOfTweetsForTopicIdWithSentiment')
            ->willReturn(20);

        $analyser = new ResultsAnalyser($mockTweetRepo, $mockUserRepo, $mockTopicRepo);

        $result = $analyser->getResultsForTopic('test');

        $this->assertInstanceOf(ResultsObject::class, $result);
    }

    public function testGetResultsForHashtag()
    {
        $mockAnalysisTopic = $this->getMockBuilder(AnalysisTopic::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockAnalysisTopic->expects($this->exactly(2))->method('getId')
            ->willReturn(1);

        $mockUserRepo = $this->getMockBuilder(AnalysisUserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockTopicRepo = $this->getMockBuilder(AnalysisTopicRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockTopicRepo->expects($this->once())->method('findOneBy')
            ->willReturn($mockAnalysisTopic);

        $mockTweetRepo = $this->getMockBuilder(TweetRepository::class)
            ->setMethods(['getNumberOfTweetsForTopicIdWithSentiment'])
            ->disableOriginalConstructor()
            ->getMock();
        $mockTweetRepo->expects($this->exactly(2))->method('getNumberOfTweetsForTopicIdWithSentiment')
            ->willReturn(20);

        $analyser = new ResultsAnalyser($mockTweetRepo, $mockUserRepo, $mockTopicRepo);

        $result = $analyser->getResultsForHashtag('test');

        $this->assertInstanceOf(ResultsObject::class, $result);
    }

    public function testGetNewTweetsForTopic()
    {
        $mockAnalysisTopic = $this->getMockBuilder(AnalysisTopic::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockAnalysisTopic->expects($this->exactly(2))->method('getId')
            ->willReturn(1);

        $mockUserRepo = $this->getMockBuilder(AnalysisUserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockTopicRepo = $this->getMockBuilder(AnalysisTopicRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockTopicRepo->expects($this->once())->method('find')
            ->willReturn($mockAnalysisTopic);

        $mockTweets = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockTweetRepo = $this->getMockBuilder(TweetRepository::class)
            ->setMethods(['getNumberOfTweetsForTopicIdWithSentiment', 'findAllTweetsForTopicIdWithTweetIdGreaterThan'])
            ->disableOriginalConstructor()
            ->getMock();
        $mockTweetRepo->expects($this->exactly(2))->method('getNumberOfTweetsForTopicIdWithSentiment')
            ->willReturn(20);
        $mockTweetRepo->expects($this->once())->method('findAllTweetsForTopicIdWithTweetIdGreaterThan')
            ->willReturn($mockTweets);

        $analyser = new ResultsAnalyser($mockTweetRepo, $mockUserRepo, $mockTopicRepo);

        $result = $analyser->getNewTweetsForTopic(1, 1);

        $this->assertInstanceOf(ResultsObject::class, $result);
    }

    public function testGetNewTweetsForUser()
    {
        $mockAnalysisUser = $this->getMockBuilder(AnalysisUser::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockAnalysisUser->expects($this->exactly(2))->method('getId')
            ->willReturn(1);

        $mockUserRepo = $this->getMockBuilder(AnalysisUserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockUserRepo->expects($this->once())->method('find')
            ->willReturn($mockAnalysisUser);

        $mockTopicRepo = $this->getMockBuilder(AnalysisTopicRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockTweets = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockTweetRepo = $this->getMockBuilder(TweetRepository::class)
            ->setMethods(['getNumberOfTweetsForUserIdWithSentiment', 'findAllTweetsForUserIdWithTweetIdGreaterThan'])
            ->disableOriginalConstructor()
            ->getMock();
        $mockTweetRepo->expects($this->exactly(2))->method('getNumberOfTweetsForUserIdWithSentiment')
            ->willReturn(20);
        $mockTweetRepo->expects($this->once())->method('findAllTweetsForUserIdWithTweetIdGreaterThan')
            ->willReturn($mockTweets);

        $analyser = new ResultsAnalyser($mockTweetRepo, $mockUserRepo, $mockTopicRepo);

        $result = $analyser->getNewTweetsForUser(1, 1);

        $this->assertInstanceOf(ResultsObject::class, $result);
    }
}
