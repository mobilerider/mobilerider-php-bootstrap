<?php

namespace Mr\Bootstrap\Http\Filtering;


class PrettusL5QueryBuilder extends AbstractQueryBuilder
{
    public function toArray()
    {
        $result = [];

        if ($this->filters) {
            // https://github.com/andersao/l5-repository
            // ex: http://prettus.local/users?search=name:John;email:john@gmail.com&searchFields=name:like;email:=
            $searchFields = [];

            $result['search'] = implode(';', array_map(function(array $filter) use (&$searchFields) {
                if (count($filter) == 3 && $filter[1] != self::OP_EQUAL) {
                    $searchFields[] = "{$filter[0]}:{$filter[1]}";
                }

                return "{$filter[0]}:{$filter[count($filter) - 1]}";
            }, $this->filters));

            if ($searchFields) {
                $result['searchFields'] = implode(';', $searchFields);
            }
        }

        return $result;
    }
}