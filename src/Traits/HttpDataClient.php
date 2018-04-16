<?php

namespace Mr\Bootstrap\Traits;

use Mr\Bootstrap\Interfaces\DataTransformerInterface;

trait HttpDataClient
{
    /**
     * @var DataTransformerInterface
     */
    protected $transformer;

    public function setDataTransformer(DataTransformerInterface $transformer)
    {
        $this->transformer = $transformer;
    }

    public function requestData($method, $uri, array $options = [])
    {
        $response = $this->{'request'}($method, $uri, $options);

        if ($response->getStatusCode() != 200) {
            throw new \RuntimeException('Request failed: ' . $response->getReasonPhrase());
        }

        $contents = $response->getBody()->getContents();

        if (! $contents) {
            return [];
        }

        return $this->transformer ? $this->transformer->toArray($contents) : $contents;
    }

    public function getData($uri, array $params = [], array $options = [])
    {
        $options['query'] = $params;

        return $this->requestData('GET', $uri, $options);
    }

    public function postData($uri, array $data, array $options = [])
    {
        $options['body'] = $this->transformer->transform($data);

        return $this->requestData('POST', $uri, $options);
    }

    public function putData($uri, array $data, array $options = [])
    {
        $options['body'] = $this->transformer->transform($data);

        return $this->requestData('PUT', $uri, $options);
    }
}