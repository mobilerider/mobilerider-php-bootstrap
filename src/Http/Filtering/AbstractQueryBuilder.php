<?php

namespace Mr\Bootstrap\Http\Filtering;

use Mr\Bootstrap\Interfaces\QueryBuilderInterface;

abstract class AbstractQueryBuilder implements QueryBuilderInterface
{
    const OP_EQUAL = '=';

    protected $filters;
    protected $limit;
    protected $offset;

    /**
     * @inheritdoc
     */
    public function __construct(array $filters = [], $limit = null)
    {
        $this->where($filters);
        
        if ($limit) {
            limit($limit);
        }
    }

    public function where($field, $operator = null, $value = null)
    {
        if (! $field) {
            return $this;
        }

        if (! is_array($field)) {
            $filters = [[func_get_args()]];
        } else {
            $filters = $field;
        }

        foreach ($filters as $index => $filter) {
            if (! is_int($index)) {
                $filter = [$index, $filter];
            }

            $this->filters[] = $filter;
        }
        
        return $this;
    }

    public function limit($limit, $offset = null)
    {
        $this->limit = $limit;
        $this->offset = $offset;

        return $this;
    }

    /**
     * Returns filters and pagination data combined into one same array
     * 
     * @return array
     */
    abstract public function toArray();

    public function __toString()
    {
        return http_build_query($this->toArray());
    }
}