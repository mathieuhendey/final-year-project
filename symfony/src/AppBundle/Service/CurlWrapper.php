<?php

/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Service;


class CurlWrapper
{
    private $curl;

    public function __construct()
    {
        $this->curl = curl_init();
    }

    public function makeGetRequest(string $path): string
    {
        curl_setopt_array($this->curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_URL => $path
        ));

        curl_exec($this->curl);
        $responseCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        curl_close($this->curl);

        return $responseCode;
    }
}