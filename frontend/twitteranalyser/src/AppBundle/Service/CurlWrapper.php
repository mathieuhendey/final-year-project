<?php

/**
 * Part of the AJ02 project at Queen's University Belfast.
 *
 * PHP version 7
 *
 * @see https://gitlab.eeecs.qub.ac.uk/40100521/AJ02
 */

namespace AppBundle\Service;

/**
 * Simple wrapper around curl to make unit testing easier.
 */
class CurlWrapper
{
    /**
     * @param string $path
     *
     * @return mixed The body or false on error
     */
    public function makeGetRequest(string $path)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $path,
        ));

        $response = curl_exec($curl);

        $body = json_decode($response, true);

        curl_close($curl);

        return $body;
    }
}
