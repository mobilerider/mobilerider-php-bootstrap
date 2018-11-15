<?php

namespace Mr\Bootstrap\Http\Filtering;


class SimpleQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $params = [];

        foreach ($this->filters as $f) {
            // Ignore operators
            $params[$f[0]] = $f[count($f) - 1];
        }

        if ($this->limit) {
            $params['limit'] = $this->limit;
        }

        if ($this->offset) {
            $params['offset'] = $this->offset;
        }

        return $params;
    }
}