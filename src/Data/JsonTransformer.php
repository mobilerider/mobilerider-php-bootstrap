<?php

namespace Mr\Bootstrap\Data;



use Mr\Bootstrap\Interfaces\DataTransformerInterface;

class JsonTransformer implements DataTransformerInterface
{
    public function transform(array $data, $pretty = false)
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

    public function toArray($stream)
    {
        return \json_decode($stream, true);
    }
}