<?php

namespace Resova;

use ErrorException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Resova\Exceptions\EmptyResults;

/**
 * @author  Paul Rock <paul@drteam.rocks>
 * @link    https://drteam.rocks
 * @license MIT
 * @package Resova
 */
trait HttpTrait
{
    /**
     * Initial state of some variables
     *
     * @var null|\GuzzleHttp\Client
     */
    public $client;

    /**
     * Object of main config
     *
     * @var \Resova\Config
     */
    public $config;

    /**
     * Request executor with timeout and repeat tries
     *
     * @param string $type   Request method
     * @param string $url    endpoint url
     * @param mixed  $params List of parameters
     *
     * @return \Psr\Http\Message\ResponseInterface|null
     * @throws \ErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function repeatRequest(string $type, string $url, $params): ?ResponseInterface
    {
        $type = strtoupper($type);

        for ($i = 1; $i < $this->config->get('tries'); $i++) {

            if ($this->config->get('verbose')) {
                error_log("[$type] " . $this->config->get('base_uri') . $url);
            }

            if ($params === null) {
                // Execute the request to server
                $result = $this->client->request($type, $this->config->get('base_uri') . $url);
            } else {
                // Execute the request to server
                $result = $this->client->request($type, $this->config->get('base_uri') . $url, [RequestOptions::FORM_PARAMS => $params->toArray()]);
            }

            // Check the code status
            $code   = $result->getStatusCode();
            $reason = $result->getReasonPhrase();

            // If success response from server
            if ($code === 200 || $code === 201) {
                return $result;
            }

            // If not "too many requests", then probably some bug on remote or our side
            if ($code !== 429) {
                throw new ErrorException($code . ' ' . $reason);
            }

            // Waiting in seconds
            sleep($this->config->get('seconds'));
        }

        // Return false if loop is done but no answer from server
        return null;
    }

    /**
     * Execute request and return response
     *
     * @return null|object Array with data or NULL if error
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ErrorException
     * @throws \Resova\Exceptions\EmptyResults
     */
    public function exec()
    {
        return $this->doRequest($this->type, $this->endpoint, $this->params, false);
    }

    /**
     * Execute query and return RAW response from remote API
     *
     * @return null|\Psr\Http\Message\ResponseInterface RAW response or NULL if error
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ErrorException
     * @throws \Resova\Exceptions\EmptyResults
     */
    public function raw(): ?ResponseInterface
    {
        return $this->doRequest($this->type, $this->endpoint, $this->params, true);
    }

    /**
     * Make the request and analyze the result
     *
     * @param string $type     Request method
     * @param string $endpoint Api request endpoint
     * @param mixed  $params   List of parameters
     * @param bool   $raw      Return data in raw format
     *
     * @return null|object|ResponseInterface Array with data, RAW response or NULL if error
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ErrorException
     * @throws \Resova\Exceptions\EmptyResults
     */
    private function doRequest($type, $endpoint, $params = null, bool $raw = false)
    {
        // Null by default
        $response = null;

        // Execute the request to server
        $result = $this->repeatRequest($type, $endpoint, $params);

        // If debug then return Guzzle object
        if ($this->config->get('debug') === true) {
            return $result;
        }

        if (null === $result) {
            throw new EmptyResults('Empty results returned from Resova API');
        }

        // Return RAW result if required
        return $raw ? $result : json_decode($result->getBody(), false);
    }

}
