<?php

namespace Mr\Bootstrap\Interfaces;


interface QueryBuilderInterface
{
    /**
     * QueryBuilderInterface constructor.
     * @param array $filters
     * @param array $pagination
     */
    public function __construct(array $filters = [], array $pagination = []);

    /**
     * @param $field
     * @param $value
     * @param null $operator
     * @return static
     */
    public function where($field, $value, $operator = null);

    public function paginate(array $pagination);

    public function limit($limit, $offset = 0);

    /**
     * @return array
     */
    public function toArray();
}