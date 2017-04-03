<?php
/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7.1
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace Tests\AppBundle\Model;

use AppBundle\Entity\AnalysisEntityInterface;
use AppBundle\Model\ResultsObject;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class ResultsObjectTest extends TestCase
{
    public function testCreate()
    {
        $tweets = new ArrayCollection([1, 2, 3]);
        $mockTerm = $this->getMockBuilder(AnalysisEntityInterface::class)
            ->getMock();

        $resultsObject = new ResultsObject($tweets, $mockTerm, 100, 100);

        $this->assertInstanceOf(ResultsObject::class, $resultsObject);
        $this->assertEquals(100, $resultsObject->getNegativeTweets());
        $this->assertEquals(100, $resultsObject->getPositiveTweets());
        $this->assertInstanceOf(AnalysisEntityInterface::class, $resultsObject->getTerm());
        $this->assertInstanceOf(Collection::class, $resultsObject->getTweets());
    }
}
