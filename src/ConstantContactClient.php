<?php

namespace Classy\ConstantContact;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ConstantContactClient
 *
 * php client to request Constant Contact.

 */
class ConstantContactClient
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $guzzleClient;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $access_token;

    /**
     * @param $api_key
     * @param array $options Accept same options as Guzzle constructor
     * @throws \Exception
     */
    public function __construct($api_key, $access_token, array $config = [])
    {
        if (!is_string($api_key)) {
            throw new \Exception('api_key must be a string');
        }

        if (!is_string($access_token)) {
            throw new \Exception('api_key must be a string');
        }

        $this->apiKey = $api_key;
        $this->access_token = $access_token;

        $config = array_merge($config, [
            'base_uri' => 'https://api.constantcontact.com/v2/',
            'headers'  => ['Authorization' => 'Bearer ' . $this->access_token]
        ]);

        $this->guzzleClient = new \GuzzleHttp\Client($config);
    }

    /**
     * Quickly Grab Data.
     *
     * @param $uri
     * @param array $options
     * @throws RequestException
     *
     * @return mixed
     */
    public function getData($uri, $options = [])
    {
        return json_decode($this->request('GET', $uri, $options)->getBody()->getContents());
    }

    /**
     * Perform a request
     *
     * @param $method
     * @param string $uri
     * @param array $options
     * @throws RequestException
     *
     * @return mixed|ResponseInterface
     */
    public function request($method, $uri = '', array $options = [])
    {
        $options = array_merge($options, [
            'query' => 'api_key=' . $this->apiKey
        ]);

        try {
            $response = $this->guzzleClient->request($method, $uri, $options);
        } catch (ClientException $e) {
            throw new RequestException($e);
        }
        return $response;
    }

    /**
     * Forward any other call to guzzle client.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->guzzleClient, $method], $parameters);
    }

    /**
     * @return \GuzzleHttp\ClientInterface
     */
    public function getGuzzleClient()
    {
        return $this->guzzleClient;
    }

    /**
     *  This is for testing basically Mocking.
     *
     * @param $client
     */
    public function setGuzzleClient($client)
    {
        $this->guzzleClient = $client;
    }
}
