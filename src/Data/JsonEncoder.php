<?php

namespace Mr\Bootstrap\Data;

use Mr\Bootstrap\Interfaces\ArrayEncoder;

class JsonEncoder implements ArrayEncoder
{
    protected $options = JSON_UNESCAPED_UNICODE 
        | JSON_UNESCAPED_SLASHES 
        | JSON_BIGINT_AS_STRING;

    public function __construct($options = null)
    {
        if (! is_null($options)) {
            $this->options = $options;
        }
    }

    public function encode(array $data, $pretty = false)
    {
        $options = $this->options;

        if ($pretty) {
            $options = $options | JSON_PRETTY_PRINT;
        }

        return \json_encode(
            $data,
            $options
        );
    }

    public function decode($stream, $asObject = false)
    {
        return \json_decode($stream, !$asObject, 512, $this->options);
    }
}