<?php

namespace Mr\Bootstrap\Http\Filtering;


class MrApiQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $params = [];

        foreach ($this->filters as $f) {
            // Ignore operators
            switch (count($f)) {
            case 3:
                // title__contains=test
                $name = $f[0] . "__" . $f[1];
                $params[$name] = $f[2];
                break;
            case 2:
                $params[$f[0]] = $f[1];
                break;
            case 1:
                $params[$f[0]] = 1;
                break;
            default:
                throw new \InvalidArgumentException("Invalid number of filters");
            }
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