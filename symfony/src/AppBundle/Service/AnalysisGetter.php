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
use Symfony\Component\HttpFoundation\Request;

class AnalysisGetter
{
    const API_URL = 'http://python/tweets';
    const TYPE_PARAM = 'type';
    const TERM_PARAM = 'term';
    const EXEC_TIME_PARAM = 'exec_time';
    const EXEC_NUMBER_PARAM = 'exec_number';

    const USER_RESPONSE_BODY_KEY = 'user_id';
    const TOPIC_RESPONSE_BODY_KEY = 'topic_id';

    const TYPE_PARAM_TOPIC_VALUE = 'topic';
    const TYPE_PARAM_USER_VALUE = 'user';

    const DEFAULT_EXEC_NUMBER = '200';
    const DEFAULT_EXEC_TIME = '300';

    /**
     * @var CurlWrapper
     */
    private $curl;

    private $logger;

    public function __construct(CurlWrapper $curl, $logger)
    {
        $this->curl = $curl;
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     *
     * @return AnalysisObject if analysis was successfully started, false if not
     */
    public function startAnalysis(Request $request): AnalysisObject
    {
        $filterTerm = $request->get(self::TERM_PARAM);
        $filterType = $this->getFilterType($filterTerm);
        $execTime = $request->get(self::EXEC_TIME_PARAM);
        $execNumber = $request->get(self::EXEC_NUMBER_PARAM);

        $url = $this->assembleUrl($filterType, $filterTerm, $execTime, $execNumber);
        $response = $this->curl->makeGetRequest($url);

        if (isNull($response)) {
            return new AnalysisObject(false, 0, true);
        }
        if (array_key_exists(self::TOPIC_RESPONSE_BODY_KEY, $response)) {
            return new AnalysisObject(true, $response[self::TOPIC_RESPONSE_BODY_KEY]);
        } elseif (array_key_exists(self::USER_RESPONSE_BODY_KEY, $response)) {
            return new AnalysisObject(false, $response[self::USER_RESPONSE_BODY_KEY]);
        }

        return new AnalysisObject(false, 0, true);
    }

    /**
     * @param string      $filterType
     * @param string      $filterTerm
     * @param string|null $execTime
     * @param string|null $execNumber
     *
     * @return string
     */
    private function assembleUrl(
        string $filterType,
        string $filterTerm,
        ? string $execTime,
        ? string $execNumber
    ) : string {
        if ($filterType == self::TYPE_PARAM_USER_VALUE) {
            $filterTerm = str_replace('@', '', $filterTerm);
        } elseif ($filterType == self::TYPE_PARAM_TOPIC_VALUE) {
            $filterTerm = rawurlencode($filterTerm);
        }

        if (!$execTime) {
            $execTime = self::DEFAULT_EXEC_TIME;
        }

        if (!$execNumber) {
            $execNumber = self::DEFAULT_EXEC_NUMBER;
        }

        $url = self::API_URL.'?'
            .self::TYPE_PARAM.'='.$filterType.'&'
            .self::TERM_PARAM.'='.$filterTerm.'&'
            .self::EXEC_TIME_PARAM.'='.$execTime.'&'
            .self::EXEC_NUMBER_PARAM.'='.$execNumber;

        return $url;
    }

    /**
     * @param string $filterTerm The filter term to be streamed
     *
     * @return string The type of the filter term, user or topic
     */
    private function getFilterType(string $filterTerm) : string
    {
        if ($filterTerm[0] == '@') {
            return self::TYPE_PARAM_USER_VALUE;
        } else {
            return self::TYPE_PARAM_TOPIC_VALUE;
        }
    }
}
