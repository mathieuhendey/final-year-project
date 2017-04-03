<?php
/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7.1
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Service;

use AppBundle\Entity\AnalysisEntityInterface;
use AppBundle\Model\ResultsObject;
use GuzzleHttp\Client;

class CurrentAnalysesChecker
{
    private const API_URI = 'http://api/current_analyses';

    /**
     * @var Client
     */
    private $guzzleClient;

    public function __construct(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @param ResultsObject $resultsObject
     *
     * @return bool
     */
    public function checkIfAnalysisIsRunning(ResultsObject $resultsObject): bool
    {
        $term = $resultsObject->getTerm();

        $paramKey = $term->getType() == AnalysisEntityInterface::USER_TYPE
            ? 'analysis_user_id'
            : 'analysis_topic_id';

        $response = $this->guzzleClient->request('GET', self::API_URI, [
            'query' => [
                $paramKey => $term->getId(),
            ],
        ]);

        $responseBody = json_decode($response->getBody(), true);

        return (bool) $responseBody['currently_analysing'];
    }

    /**
     * @return array
     */
    public function getAllRunningAnalyses(): array
    {
        $ret = [];

        $response = $this->guzzleClient->request('GET', self::API_URI, [
            'query' => [
                'all' => true,
            ],
        ]);

        $responseBody = json_decode($response->getBody(), true);

        foreach ($responseBody['current_analyses'] as $item) {
            $ret[] = $item;
        }

        return $ret;
    }

    /**
     * @param int    $termId
     * @param string $termType
     *
     * @return bool
     */
    public function checkIfAnalysisIsRunningWithIdAndType(int $termId, string $termType): bool
    {
        $paramKey = $termType == AnalysisEntityInterface::USER_TYPE
            ? 'analysis_user_id'
            : 'analysis_topic_id';

        $response = $this->guzzleClient->request('GET', self::API_URI, [
            'query' => [
                $paramKey => $termId,
            ],
        ]);

        $responseBody = json_decode($response->getBody(), true);

        return (bool) $responseBody['currently_analysing'];
    }
}
