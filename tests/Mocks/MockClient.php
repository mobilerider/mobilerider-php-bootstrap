<?php

namespace Mr\BootstrapTests\Mocks;

use RuntimeException;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;
use Mr\Bootstrap\Traits\HttpDataClient;
use Mr\Bootstrap\Interfaces\HttpDataClientInterface;
use Mr\Bootstrap\Data\JsonEncoder;

/**
 * Created by PhpStorm.
 * User: michel
 * Date: 7/28/17
 * Time: 6:28 PM
 */
class MockClient extends Client implements HttpDataClientInterface
{
    use HttpDataClient;

    public function __construct(array $config = [])
    {
        if (! isset($config['responses'])) {
            throw new RuntimeException('Mock responses need to be provided');
        }

        $responses = [];

        foreach ($config['responses'] as $response) {
            if (!isset($response['error'])) {
                $responses[] = new Response($response['code'], $response['headers'], $response['body']);
            } else {
                $responses[] = new RequestException($response['message'], new Request($response['request']['method'], $response['request']['body']));
            }
        }

        $mock = new MockHandler($responses);

        $config['handler'] = HandlerStack::create($mock);

        parent::__construct($config);

        $this->setDataEncoder(new JsonEncoder());
    }
}