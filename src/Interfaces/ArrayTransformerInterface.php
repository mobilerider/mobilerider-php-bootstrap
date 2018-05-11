<?php

namespace Mr\Bootstrap\Interfaces;


interface ArrayTransformerInterface
{
    /**
     * Transform array data
     *
     * @param array $data
     * @return array
     */
    public function transform(array $data);
}