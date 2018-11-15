<?php

namespace Mr\Bootstrap\Interfaces;


interface QueryBuilderInterface
{
    /**
     * QueryBuilderInterface constructor.
     * @param array $filters
     * @param array $pagination
     */
    public function __construct(array $filters = [], $limit = null);

    /**
     * @param $field
     * @param $value
     * @param null $operator
     * @return static
     */
    public function where($field, $operator = null, $value = null);

    public function limit($limit, $offset = 0);

    /**
     * @return array
     */
    public function toArray();
}