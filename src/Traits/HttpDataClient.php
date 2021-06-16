<?php

namespace Mr\Bootstrap\Traits;

use Mr\Bootstrap\Interfaces\ArrayEncoder;

trait HttpDataClient
{
    /**
     * @var ArrayEncoder
     */
    protected $encoder;

    public function setDataEncoder(ArrayEncoder $encoder)
    {
        $this->encoder = $encoder;
    }

    public function requestData($method, $uri, array $options = [])
    {
        // Parse query parameters out of uri too
        if (($qIndex = strpos($uri, "?")) !== false) {
            $queryParts = parse_str(substr($uri, $qIndex + 1));
            $options['query'] = array_merge($options['query'], $queryParts);
        }

        $response = $this->{'request'}($method, $uri, $options);
        $code = $response->getStatusCode();

        if ($code < 200 || $code > 299) {
            throw new \RuntimeException(
                'Request failed: ' . $response->getReasonPhrase()
            );
        }

        $contents = $response->getBody()->getContents();

        if (! $contents) {
            return [];
        }

        return $this->encoder ? $this->encoder->decode($contents) : $contents;
    }

    public function getData($uri, array $params = [], array $options = [])
    {
        $options['query'] = $params;

        return $this->requestData('GET', $uri, $options);
    }

    public function postData(
        $uri, array $data, array $params = [], array $options = []
    ) {
        $options['body'] = $this->encoder->encode($data);

        if ($params) {
            $options['query'] = $params;
        }

        return $this->requestData('POST', $uri, $options);
    }

    public function putData(
        $uri, array $data, array $params = [], array $options = []
    ) {
        $options['body'] = $this->encoder->encode($data);

        if ($params) {
            $options['query'] = $params;
        }

        return $this->requestData('PUT', $uri, $options);
    }
}