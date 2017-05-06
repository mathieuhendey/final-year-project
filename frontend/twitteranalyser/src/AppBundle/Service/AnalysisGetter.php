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
    private const API_URL = 'http://api/tweets';
    private const TYPE_PARAM = 'type';
    private const TERM_PARAM = 'term';
    private const EXEC_TIME_PARAM = 'exec_time';
    private const EXEC_NUMBER_PARAM = 'exec_number';
    private const SHOULD_REANALYSE_PARAM = 'should_reanalyse';

    private const USER_RESPONSE_BODY_KEY = 'user_id';
    private const TOPIC_RESPONSE_BODY_KEY = 'topic_id';
    private const HASHTAG_RESPONSE_BODY_KEY = 'is_hashtag';
    private const RATE_LIMITED_KEY = 'rate_limited';
    private const TIME_LEFT_ON_STREAM_KEY = 'time_left_on_stream';
    private const ALREADY_ANALYSED_KEY = 'already_analysed';

    private const TYPE_PARAM_TOPIC_VALUE = 'topic';
    private const TYPE_PARAM_USER_VALUE = 'user';

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

        $response = $this->guzzleClient->request(
            'GET', self::API_URL, [
            'query' => [
                self::TYPE_PARAM => $filterType,
                self::TERM_PARAM => str_replace('@', '', $filterTerm),
                self::EXEC_TIME_PARAM => $execTime,
                self::EXEC_NUMBER_PARAM => $execNumber,
                self::SHOULD_REANALYSE_PARAM => $shouldReanalyse,
                ],
            ]
        );

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
