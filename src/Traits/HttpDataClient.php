<?php

namespace Mr\Bootstrap\Traits;

use AwsElemental\Sdk\Interfaces\DataTransformerInterface;

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

        // Once you read contents from the response is gone (stream)
        return $this->transformer->toArray($response->getBody()->getContents());
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