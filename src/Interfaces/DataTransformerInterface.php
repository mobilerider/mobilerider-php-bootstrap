<?php

namespace Mr\Bootstrap\Interfaces;


interface DataTransformerInterface
{
    /**
     * Transform array data
     *
     * @param array $data
     * @param bool $pretty
     * @return mixed
     */
    public function transform(array $data, $pretty = false);

    /**
     * Return array transformed back from given string
     *
     * @param $stream
     * @return mixed
     */
    public function toArray($stream);
}