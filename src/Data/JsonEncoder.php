<?php

namespace Mr\Bootstrap\Data;

use Mr\Bootstrap\Interfaces\ArrayEncoder;

class JsonEncoder implements ArrayEncoder
{
    public function encode(array $data, $pretty = false)
    {
        $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

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
        return \json_decode($stream, true);
    }
}