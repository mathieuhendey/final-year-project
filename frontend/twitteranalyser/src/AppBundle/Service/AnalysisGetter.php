<?php

/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Service;

use AppBundle\Model\AnalysisObject;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;

class AnalysisGetter
{
    const API_URL = 'http://api/tweets';
    const TYPE_PARAM = 'type';
    const TERM_PARAM = 'term';
    const EXEC_TIME_PARAM = 'exec_time';
    const EXEC_NUMBER_PARAM = 'exec_number';

    const USER_RESPONSE_BODY_KEY = 'user_id';
    const TOPIC_RESPONSE_BODY_KEY = 'topic_id';
    const RATE_LIMITED_KEY = 'rate_limited';
    const TIME_LEFT_ON_STREAM_KEY = 'time_left_on_stream';

    const TYPE_PARAM_TOPIC_VALUE = 'topic';
    const TYPE_PARAM_USER_VALUE = 'user';

    const DEFAULT_EXEC_NUMBER = '10000';
    const DEFAULT_EXEC_TIME = '30';

    /**
     * @var Client
     */
    private $guzzleClient;

    public function __construct(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @param Request $request
     *
     * @return AnalysisObject|null
     */
    public function startAnalysis(Request $request)
    {
        $filterTerm = trim($request->get(self::TERM_PARAM));
        $filterType = $this->getFilterType($filterTerm);
        $execTime = $request->get(self::EXEC_TIME_PARAM);
        $execNumber = $request->get(self::EXEC_NUMBER_PARAM);

        $response = $this->guzzleClient->request('GET', self::API_URL, [
            'query' => [
                self::TYPE_PARAM => $filterType,
                self::TERM_PARAM => str_replace('@', '', $filterTerm),
                self::EXEC_TIME_PARAM => $execTime,
                self::EXEC_NUMBER_PARAM => $execNumber,
            ],
        ]);

        $responseBody = json_decode($response->getBody(), true);

        if (array_key_exists(self::RATE_LIMITED_KEY, $responseBody)) {
            return AnalysisObject::fromRateLimitedResponse($responseBody[self::TIME_LEFT_ON_STREAM_KEY]);
        } elseif (array_key_exists(self::TOPIC_RESPONSE_BODY_KEY, $responseBody)) {
            return AnalysisObject::fromTopicResponse($responseBody[self::TOPIC_RESPONSE_BODY_KEY]);
        } elseif (array_key_exists(self::USER_RESPONSE_BODY_KEY, $responseBody)) {
            return AnalysisObject::fromUserResponse($responseBody[self::USER_RESPONSE_BODY_KEY]);
        }

        return null;
    }

    /**
     * @param string $filterTerm the filter term to be streamed
     *
     * @return string the type of the filter term, user or topic
     */
    private function getFilterType(string $filterTerm): string
    {
        if ($filterTerm[0] == '@') {
            return self::TYPE_PARAM_USER_VALUE;
        } else {
            return self::TYPE_PARAM_TOPIC_VALUE;
        }
    }
}
