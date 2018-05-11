<?php

namespace Mr\Bootstrap\Http\Filtering;

use Mr\Bootstrap\Interfaces\QueryBuilderInterface;

abstract class AbstractQueryFilter implements QueryBuilderInterface
{
    const OP_EQUAL = '=';
    const OP_LIKE = 'like';

    protected $filters;
    protected $pagination;

    /**
     * @inheritdoc
     */
    public function __construct(array $filters = [], array $pagination = [])
    {
        $this->setFilters($filters);
        $this->paginate($pagination);
    }

    protected function setFilters(array $filters)
    {
        foreach ($filters as $filter) {
            call_user_func_array([$this, 'addFilter'], $filter);
        }
    }

    protected function addFilter($field, $operator, $value = null)
    {
        if (func_num_args() < 3) {
            $value = $operator;
            $operator = self::OP_EQUAL;
        }

        $this->filters[] = [$field, $operator, $value];
    }

    public function where($field, $operator, $value = null)
    {
        if (is_array($field)) {
            $this->setFilters($field);
        } else {
            $this->addFilter($field, $operator, $value);
        }

        return $this;
    }

    public function paginate(array $pagination)
    {
        $this->pagination = $pagination;

        return $this;
    }

    public function limit($limit, $offset = null)
    {
        $this->pagination['limit'] = $limit;

        if (! is_null($offset)) {
            $this->pagination['offset'] = $offset;
        }

        return $this;
    }

    /**
     * Returns filters and pagination data combined into one same array
     * @return array
     */
    public abstract function toArray();
}