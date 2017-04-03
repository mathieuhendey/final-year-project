<?php
/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7.1
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Test\Service;

use AppBundle\Entity\AnalysisEntityInterface;
use AppBundle\Entity\AnalysisTopic;
use AppBundle\Model\ResultsObject;
use AppBundle\Service\CurrentAnalysesChecker;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class CurrentAnalysesCheckerTest extends TestCase
{
    /**
     * @param string $responseBody
     * @param bool $expected
     *
     * @dataProvider checkIfAnalysisIsRunningProvider
     */
    public function testCheckIfAnalysisIsRunning(string $responseBody, bool $expected)
    {
        $mockResponse = $this->getMockBuilder(ResponseInterface::class)
            ->getMock();
        $mockResponse->method('getBody')
            ->with()
            ->willReturn($responseBody);

        /**
         * @var Client|\PHPUnit_Framework_MockObject_MockObject $mockGuzzleClient
         */
        $mockGuzzleClient = $this->getMockBuilder(Client::class)
            ->setMethods(['request'])
            ->getMock();
        $mockGuzzleClient->method('request')
            ->willReturn($mockResponse);

        $mockTerm = $this->getMockBuilder(AnalysisTopic::class)
            ->disableOriginalConstructor()
            ->setMethods(['getType', 'getId'])
            ->getMock();
        $mockTerm->method('getId')
            ->willReturn(1);
        $mockTerm->method('getType')
            ->willReturn('user');

        $mockResultsObject = $this->getMockBuilder(ResultsObject::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTerm'])
            ->getMock();
        $mockResultsObject->method('getTerm')
            ->willReturn($mockTerm);


        $currentAnalysesChecker = new CurrentAnalysesChecker($mockGuzzleClient);
        $result = $currentAnalysesChecker->checkIfAnalysisIsRunning($mockResultsObject);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function checkIfAnalysisIsRunningProvider(): array
    {
        return [
            ['{"currently_analysing": true}', true],
            ['{"currently_analysing": false}', false],
        ];
    }

    public function testGetAllRunningAnalyses()
    {
        $mockResponse = $this->getMockBuilder(ResponseInterface::class)
            ->getMock();
        $mockResponse->method('getBody')
            ->with()
            ->willReturn('{
                                  "current_analyses": [
                                    {
                                      "type": "topic",
                                      "analysis_topic_id": 1
                                    }
                                  ]
                                }');

        /**
         * @var Client|\PHPUnit_Framework_MockObject_MockObject $mockGuzzleClient
         */
        $mockGuzzleClient = $this->getMockBuilder(Client::class)
            ->setMethods(['request'])
            ->getMock();
        $mockGuzzleClient->method('request')
            ->willReturn($mockResponse);

        $currentAnalysesChecker = new CurrentAnalysesChecker($mockGuzzleClient);

        $this->assertInternalType('array', $currentAnalysesChecker->getAllRunningAnalyses());
    }

    /**
     * @param string $responseBody
     * @param bool $expected
     *
     * @dataProvider checkIfAnalysisIsRunningWithIdAndTypeProvider
     */
    public function testCheckIfAnalysisIsRunningWithIdAndType(string $responseBody, bool $expected)
    {
        $mockResponse = $this->getMockBuilder(ResponseInterface::class)
            ->getMock();
        $mockResponse->method('getBody')
            ->with()
            ->willReturn($responseBody);

        /**
         * @var Client|\PHPUnit_Framework_MockObject_MockObject $mockGuzzleClient
         */
        $mockGuzzleClient = $this->getMockBuilder(Client::class)
            ->setMethods(['request'])
            ->getMock();
        $mockGuzzleClient->method('request')
            ->willReturn($mockResponse);

        $currentAnalysesChecker = new CurrentAnalysesChecker($mockGuzzleClient);

        $result = $currentAnalysesChecker->checkIfAnalysisIsRunningWithIdAndType(1, 'user');

        $this->assertEquals($expected, $result);
    }

    public function checkIfAnalysisIsRunningWithIdAndTypeProvider(): array
    {
        return [
            ['{"currently_analysing": true}', true],
            ['{"currently_analysing": false}', false],
        ];
    }

}
