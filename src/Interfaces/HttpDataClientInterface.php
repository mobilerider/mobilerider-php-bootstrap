<?php

namespace Mr\Bootstrap\Interfaces;


interface HttpDataClientInterface
{
    public function setDataEncoder(ArrayEncoder $encoder);
    public function requestData($method, $uri, array $options = []);
    public function getData($uri, array $params = [], array $options = []);
    public function postData($uri, array $data, array $options = []);
    public function putData($uri, array $data, array $options = []);
}