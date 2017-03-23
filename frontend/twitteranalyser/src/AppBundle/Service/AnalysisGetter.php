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
    const SHOULD_REANALYSE_PARAM = 'should_reanalyse';

    const USER_RESPONSE_BODY_KEY = 'user_id';
    const TOPIC_RESPONSE_BODY_KEY = 'topic_id';
    const HASHTAG_RESPONSE_BODY_KEY = 'is_hashtag';
    const RATE_LIMITED_KEY = 'rate_limited';
    const TIME_LEFT_ON_STREAM_KEY = 'time_left_on_stream';
    const ALREADY_ANALYSED_KEY = 'already_analysed';

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
    public function startAnalysis(Request $request): ?AnalysisObject
    {
        $filterTerm = trim($request->get(self::TERM_PARAM));
        $filterType = $this->getFilterType($filterTerm);
        $execTime = $request->get(self::EXEC_TIME_PARAM);
        $execNumber = $request->get(self::EXEC_NUMBER_PARAM);
        $shouldReanalyse = $request->get(self::SHOULD_REANALYSE_PARAM, false);

        $response = $this->guzzleClient->request('GET', self::API_URL, [
            'query' => [
                self::TYPE_PARAM => $filterType,
                self::TERM_PARAM => str_replace('@', '', $filterTerm),
                self::EXEC_TIME_PARAM => $execTime,
                self::EXEC_NUMBER_PARAM => $execNumber,
                self::SHOULD_REANALYSE_PARAM => $shouldReanalyse,
            ],
        ]);

        $result = $this->handleResponseBody(json_decode($response->getBody(), true));

        return $result;
    }

    /**
     * @param string $filterTerm the filter term to be streamed
     *
     * @return string the type of the filter term, user or topic
     */
    private function getFilterType(string $filterTerm): string
    {
        return $filterTerm[0] == '@' ? self::TYPE_PARAM_USER_VALUE : self::TYPE_PARAM_TOPIC_VALUE;
    }

    /**
     * @param array $responseBody
     *
     * @return AnalysisObject|null
     */
    private function handleResponseBody(array $responseBody): ?AnalysisObject
    {
        if (array_key_exists(self::RATE_LIMITED_KEY, $responseBody)) {
            return AnalysisObject::fromRateLimitedResponse($responseBody[self::TIME_LEFT_ON_STREAM_KEY]);
        } elseif (array_key_exists(self::TOPIC_RESPONSE_BODY_KEY, $responseBody)) {
            $id = $responseBody[self::TOPIC_RESPONSE_BODY_KEY];
            $alreadyAnalysed = $responseBody[self::ALREADY_ANALYSED_KEY];

            return $responseBody[self::HASHTAG_RESPONSE_BODY_KEY] === true
                ? AnalysisObject::fromHashtagResponse($id, $alreadyAnalysed)
                : AnalysisObject::fromTopicResponse($id, $alreadyAnalysed);
        } elseif (array_key_exists(self::USER_RESPONSE_BODY_KEY, $responseBody)) {
            $id = $responseBody[self::USER_RESPONSE_BODY_KEY];
            $alreadyAnalysed = $responseBody[self::ALREADY_ANALYSED_KEY];

            return AnalysisObject::fromUserResponse($id, $alreadyAnalysed);
        }

        return null;
    }
}
