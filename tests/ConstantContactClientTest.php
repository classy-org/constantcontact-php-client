<?php

namespace Classy\ConstantContact\Tests;

use Classy\ConstantContact\ConstantContactClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit_Framework_TestCase;
use Mockery;

class ConstantContactClientTest extends PHPUnit_Framework_TestCase
{
    const API_KEY = 'key';
    const SECRET = 'check';

    /**
     * @var \Mockery\MockInterface
     */
    protected $guzzleClientMock;

    /**
     * @var ConstantContactClient
     */
    protected $client;

    /**
     * @throws \Exception
     */
    public function setUp()
    {
        parent::setUp();

        $this->client = new ConstantContactClient(self::API_KEY, self::SECRET);
        $this->guzzleClientMock = Mockery::mock($this->client->getGuzzleClient())->makePartial();
        $this->client->setGuzzleClient($this->guzzleClientMock);
    }

    /**
     * @param string $ExpectedMethod
     * @param string $ExpectedUri
     * @param null|string $ExpectedQuery
     * @param null|array $ExpectedJson
     * @param null|int $ExpectedErrorCode
     */
    public function buildMockRequest($ExpectedMethod, $ExpectedUri, $ExpectedQuery = null, $ExpectedJson = null, $ExpectedErrorCode = null)
    {
        $expectation = $this->guzzleClientMock
            ->shouldReceive('request')
            ->withArgs(
                function ($method, $uri, $options) use ($ExpectedMethod, $ExpectedUri, $ExpectedQuery, $ExpectedJson) {
                    $check = $ExpectedMethod === $method && $ExpectedUri === $uri && $ExpectedQuery === $options['query'];
                    if (isset($options['json'])) {
                        return $check && $options['json'] === $ExpectedJson;
                    }
                    return $check;
                });

        if ($ExpectedErrorCode) {
            $expectation->andThrow(
                new ClientException('Error', new Request('GET', ''), new Response(404)));
        } else {
            $expectation->andReturn(new Response(204));
        }
    }

    /**
     * @covers \Classy\ConstantContact\ConstantContactClient::request
     */
    public function testRequestNormally()
    {
        $this->buildMockRequest('GET', 'pathing', ['api_key' => self::API_KEY]);

        $response = $this->client->request('GET', 'pathing');
        $this->assertEquals(204, $response->getStatusCode());
    }

    /**
     * @covers \Classy\ConstantContact\ConstantContactClient::request
     */
    public function testClientException()
    {
        $this->buildMockRequest('POST', 'dreams', ['api_key' => self::API_KEY], ['check' => 1, 'check2' => 2], 404);

        try {
            $this->client->request('POST', 'dreams', ['body' => ['check' => 1, 'check2' => 2]]);
            $this->fail('Exception Expected');
        } catch (ClientException $e) {
            $this->assertEquals(404, $e->getCode());
        }
    }

    /**
     * @covers \Classy\ConstantContact\ConstantContactClient::addContact
     */
    public function testAddContactsException()
    {
        $this->buildMockRequest('POST', 'contacts', ['action_by' => 'ACTION_BY_OWNER', 'api_key' => self::API_KEY], ['check' => 1, 'check2' => 2], 404);

        try {
            $this->client->addContact(['check' => 1, 'check2' => 2]);
            $this->fail('Exception Expected');
        } catch (ClientException $e) {
            $this->assertEquals(404, $e->getCode());
        }
    }
}
