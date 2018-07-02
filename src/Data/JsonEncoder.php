<?php

namespace Mr\Bootstrap\Data;

use Mr\Bootstrap\Interfaces\ArrayEncoder;

class JsonEncoder implements ArrayEncoder
{
    protected $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

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

    public function decode($stream)
    {
        return \json_decode($stream, true, 256, $this->options);
    }
}