<?php

namespace Mr\Bootstrap\Interfaces;

interface HttpDataClientInterface
{
    public function setDataEncoder(ArrayEncoder $encoder);
    public function requestData($method, $uri, array $options = []);
    public function getData($uri, array $params = [], array $options = []);
    /**
     * Send POST request to given endpoint
     *
     * @param string $uri
     * @param array $data
     * @param array $params
     * @param array $options
     * 
     * @return array
     */
    public function postData(
        $uri, array $data, array $params = [], array $options = []
    );
    
    public function putData(
        $uri, array $data, array $params = [], array $options = []
    );
}