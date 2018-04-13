<?php

namespace Mr\Bootstrap\Interfaces;


use AwsElemental\Sdk\Interfaces\DataTransformerInterface;

interface HttpDataClientInterface
{
    public function setDataTransformer(DataTransformerInterface $transformer);
    public function requestData($method, $uri, array $options = []);
    public function getData($uri, array $params = [], array $options = []);
    public function postData($uri, array $data, array $options = []);
    public function putData($uri, array $data, array $options = []);
}