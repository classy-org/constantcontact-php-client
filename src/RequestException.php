<?php

namespace Classy\ConstantContact;

use Exception;
use GuzzleHttp\Exception\ClientException;

/**
 * Class RequestException
 */
class RequestException extends Exception
{
    /**
     * @var \Psr\Http\Message\RequestInterface
     */
    public $request;

    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    public $response;

    /**
     * @param ClientException $e
     */
    public function __construct(ClientException $e)
    {
        $this->request = $e->getRequest();
        $this->response = $e->getResponse();

        $message = 'Constant Contact Response error (' . $this->response->getStatusCode() . ')';
        $message .= ' on ' . $this->request->getMethod() . ' ' . $this->request->getUri()->__toString();
        $message .= ' - ' . $this->response->getBody()->getContents();

        parent::__construct($message, $this->response->getStatusCode(), $e);
    }
}
