<?php

namespace Classy;

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
    private $client;

    /**
     * @var string
     */
    public $apiKey;

    /**
     * @param $api_key
     * @param array $options Accept same options as Guzzle constructor
     */
    public function __construct($api_key, array $config = [])
    {
        if (!is_string($api_key)) {
            throw new \Exception("api_key must be a string");
        }

        $this->apiKey = $api_key;

        $config = array_merge($config, [
            'base_uri' => "https://api.constantcontact.com/v2",
        ]);

        $this->client = new \GuzzleHttp\Client($config);
    }

    /**
     * Quickly Grab Data.
     *
     * @param $uri
     * @param array $options
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
     * @return mixed|ResponseInterface
     */
    public function request($method, $uri = '', array $options = [])
    {
        $options = array_merge($options, [
            'query' => ['api_key' => $this->apiKey]
        ]);
        return $this->client->request($method, $uri, $options);
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
        return call_user_func_array([$this->client, $method], $parameters);
    }
}
