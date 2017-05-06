<?php
/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7.1
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace Tests\AppBundle\Controller;

use AppBundle\Service\Comparator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ComparisonsControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/compare', ['term_type' => 'topic', 'term_ids' => [1,2]]);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testAjaxAction()
    {
        $client = static::createClient();

        $mockComparator = $this->getMockBuilder(Comparator::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDatasets'])
            ->getMock();

        $mockComparator->method('getDatasets')->willReturn([]);

        $client->getContainer()->set('app.comparator', $mockComparator);

        $crawler = $client->request('GET', '/compare', ['term_type' => 'topic', 'term_ids' => [1,2]]);
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}