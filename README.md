# ConstantContact PHP Client [![Build Status](https://travis-ci.org/classy-org/constantcontact-php-client.png?branch=master)](https://travis-ci.org/classy-org/constantcontact-php-client)
Wrapper around Guzzle Http Client to interact with Constant Contact APIv2

## Installation

The ConstantContact API php client can be installed with [Composer](https://getcomposer.org/):

```sh
composer require classy-org/constantcontact-php-client
```

Be sure you included composer autoloader in your app:

```php
require_once '/path/to/your/project/vendor/autoload.php';
```

## Usage

```php
// Instantiate the client
$client = new \Classy\ConstantContactClient('API-KEY', 'ACCESS-TOKEN');

// Make a request. 
$httpResponse = $client->request('GET', 'contacts');
$contacts = json_decode($httpResponse->getBody()->getContents());

//Or Grab Data Quickly.
$contacts = $client->getData('contacts');

//Store a Contact Quickly.
$payload = [
    'lists' => [
        [
            'id' => (string)1
        ]
    ],
    'email_addresses' => [
        [
            'email_address' => (string)'person@constantcontact.com'
        ]
    ],
    'first_name' => (string)'My',
    'last_name'  => (string)'Name',
];
$contacts = $client->addContact($payload);

```

## Exception handling

This client is using Guzzle Http client. Exceptions are thrown when the Http response is not a 200 (OK) one:

```php
try {
    $response = $client->request('POST', 'path/to/route', ['body' => ['check' => 1]]);
} catch (Exception $e) {
    if ($e instanceof \GuzzleHttp\Exception\ConnectException) {
        // there was a networking error
    }

    if ($e instanceof \GuzzleHttp\Exception\ClientException) {
        // Mailchimp API returned a 4xx response.
        $httpStatusCode = $e->getCode();
        if ($httpStatusCode == 404) {
            // resource doesn't exist
        }
        if ($httpStatusCode == 401) {
            // you're unauthorized (api key must be invalid)
        }
        if ($httpStatusCode == 403) {
            // you're not allowed to request this endpoint
        }
        if ($httpStatusCode == 400) {
            // body payload is invalid
        }
        if (...) {
            //
        }

        $bodyResponse = $e->getResponse()->getBody()->getContents();
    }

    if ($e instanceof \GuzzleHttp\Exception\ServerException) {
        // ConstantContact returned a 5xx response, which means they experience technical difficulties.
    }
}
```