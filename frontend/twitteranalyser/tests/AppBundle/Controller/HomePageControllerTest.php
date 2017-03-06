<?php

/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\AnalysisTopic;
use AppBundle\Entity\AnalysisUser;
use AppBundle\Model\AnalysisObject;
use AppBundle\Repository\AnalysisTopicRepository;
use AppBundle\Repository\AnalysisUserRepository;
use AppBundle\Service\AnalysisGetter;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class HomePageControllerTest.
 *
 * @author Mathieu Hendey <mhendey01@qub.ac.uk>
 */
class HomePageControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testBeginAnalysisTopic()
    {
        $client = static::createClient();
        $mockAnalysisGetter = $this->getMockBuilder(AnalysisGetter::class)
            ->disableOriginalConstructor()
            ->setMethods(['startAnalysis'])
            ->getMock();

        $mockTopic = $this->getMockBuilder(AnalysisTopic::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTerm'])
            ->getMock();
        $mockTopic->expects($this->once())
            ->method('getTerm')
            ->willReturn('test');

        $mockTopicRepository = $this->getMockBuilder(AnalysisTopicRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();
        $mockTopicRepository->expects($this->once())
            ->method('find')
            ->willReturn($mockTopic);

        $mockDoctrine = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRepository'])
            ->getMock();
        $mockDoctrine->expects($this->once())
            ->method('getRepository')
            ->willReturn($mockTopicRepository);

        $analysisResult = AnalysisObject::fromTopicResponse(1);

        $mockAnalysisGetter->expects($this->once())->method('startAnalysis')
            ->willReturn($analysisResult);

        $client->getContainer()->set('app.analysis_getter', $mockAnalysisGetter);
        $client->getContainer()->set('doctrine', $mockDoctrine);

        $crawler = $client->request('GET', '/analyse');

        $this->assertTrue($client->getResponse()->isRedirection());
    }

    public function testBeginAnalysisUser()
    {
        $client = static::createClient();
        $mockAnalysisGetter = $this->getMockBuilder(AnalysisGetter::class)
            ->disableOriginalConstructor()
            ->setMethods(['startAnalysis'])
            ->getMock();

        $mockUser = $this->getMockBuilder(AnalysisUser::class)
            ->disableOriginalConstructor()
            ->setMethods(['getScreenName'])
            ->getMock();
        $mockUser->expects($this->once())
            ->method('getScreenName')
            ->willReturn('test');

        $mockUserRepository = $this->getMockBuilder(AnalysisUserRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();
        $mockUserRepository->expects($this->once())
            ->method('find')
            ->willReturn($mockUser);

        $mockDoctrine = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRepository'])
            ->getMock();
        $mockDoctrine->expects($this->once())
            ->method('getRepository')
            ->willReturn($mockUserRepository);

        $analysisResult = AnalysisObject::fromUserResponse(1);

        $mockAnalysisGetter->expects($this->once())->method('startAnalysis')
            ->willReturn($analysisResult);

        $client->getContainer()->set('app.analysis_getter', $mockAnalysisGetter);
        $client->getContainer()->set('doctrine', $mockDoctrine);

        $crawler = $client->request('GET', '/analyse');

        $this->assertTrue($client->getResponse()->isRedirection());
    }

    public function testBeginAnalysisRateLimited()
    {
        $client = static::createClient();
        $analysisResult = AnalysisObject::fromRateLimitedResponse(1);
        $mockAnalysisGetter = $this->getMockBuilder(AnalysisGetter::class)
            ->disableOriginalConstructor()
            ->setMethods(['startAnalysis'])
            ->getMock();
        $mockAnalysisGetter->expects($this->once())->method('startAnalysis')
            ->willReturn($analysisResult);

        $client->getContainer()->set('app.analysis_getter', $mockAnalysisGetter);

        $crawler = $client->request('GET', '/analyse');

        $this->assertTrue($client->getResponse()->isClientError());
    }
}
