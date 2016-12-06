<?php

namespace AppBundle\Service;

class AnalysisGetter
{
    const API_URL = 'http://python/tweets';
    /**
     * @var CurlWrapper
     */
    private $curl;

    public function __construct(CurlWrapper $curl)
    {
        $this->curl = $curl;
    }

    /**
     * @param string $filterTerm
     * @return bool true if analysis was successfully started, false if not
     */
    public function startAnalysis(string $filterTerm): bool
    {
        $httpCode = $this->curl->makeGetRequest($this->assembleUrl($filterTerm));
        if ($httpCode === "200") {
            return true;
        }

        return false;
    }

    /**
     * @param string $filterTerm
     * @return string
     */
    private function assembleUrl(string $filterTerm): string
    {
        $url = self::API_URL.'?track='.$filterTerm;

        return $url;
    }
}