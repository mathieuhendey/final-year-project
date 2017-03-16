<?php
/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace Tests\AppBundle\Service;

use AppBundle\Model\AnalysisObject;
use AppBundle\Service\AnalysisGetter;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

class AnalysisGetterTest extends \PHPUnit_Framework_TestCase
{
    private const RATE_LIMITED = 'rate_limited';
    private const TOPIC = 'topic';
    private const USER = 'user';
    private const BROKEN = 'broken';
    private const HASHTAG = 'hashtag';

    /**
     * @param string $term
     * @param string $execTime
     * @param string $execNumber
     * @param string $responseBody
     * @param string $type
     *
     * @dataProvider analysisProvider
     */
    public function testStartAnalysis(string $term, string $execTime, string $execNumber, string $responseBody, string $type)
    {
        $mockRequestReturnMap = [
            [AnalysisGetter::TERM_PARAM, null, $term],
            [AnalysisGetter::EXEC_TIME_PARAM, null, $execTime],
            [AnalysisGetter::EXEC_NUMBER_PARAM, null, $execNumber],
        ];

        /**
         * @var Request|\PHPUnit_Framework_MockObject_MockObject $mockRequest
         */
        $mockRequest = $this->getMockBuilder(Request::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $mockRequest->method('get')
            ->will($this->returnValueMap($mockRequestReturnMap));

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

        $analysisGetter = new AnalysisGetter($mockGuzzleClient);
        $result = $analysisGetter->startAnalysis($mockRequest);

        if ($type == self::RATE_LIMITED) {
            $this->assertInstanceOf(AnalysisObject::class, $result);
            $this->assertEquals(100, $result->getTimeLeftOnStream());
            $this->assertEquals(true, $result->isRateLimited());
        } elseif ($type == self::TOPIC) {
            $this->assertInstanceOf(AnalysisObject::class, $result);
            $this->assertEquals(1, $result->getId());
            $this->assertTrue($result->isTopic());
            $this->assertFalse($result->isUser());
        } elseif ($type == self::USER) {
            $this->assertInstanceOf(AnalysisObject::class, $result);
            $this->assertEquals(1, $result->getId());
            $this->assertTrue($result->isUser());
            $this->assertFalse($result->isTopic());
        } elseif ($type == self::BROKEN) {
            $this->assertNull($result);
        }
    }

    public function analysisProvider()
    {
        return [
            'rate_limited' => ['test', '60', '100', '{"rate_limited": true, "time_left_on_stream": 100}', self::RATE_LIMITED],
            'topic' => ['test', '60', '100', '{"topic_id": 1, "is_hashtag": 0}', self::TOPIC],
            'hashtag' => ['test', '60', '100', '{"topic_id": 1, "is_hashtag": 0}', self::HASHTAG],
            'user' => ['@test', '60', '100', '{"user_id": 1}', self::USER],
            'broken' => ['@test', '60', '100', '{"broken": 1}', self::BROKEN],
        ];
    }
}
