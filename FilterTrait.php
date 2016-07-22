<?php

namespace AE\Stdlib;

trait FilterTrait
{
    public function __construct()
    {
    }

    public function toValues()
    {
        $filterArray = array_filter(get_object_vars($this));
        return array_values($filterArray);
    }

    public function getFilterNames()
    {
        $filterArray = get_object_vars($this);
        $filterNames = array_keys(array_filter($filterArray));
        $sqlFragments = [];
        foreach($filterNames as $filter) {
            $sqlFragments[] = '' . $filter . '=? ';
        }
        $sql = implode("  AND  ", $sqlFragments);

        return $sql;
    }

    public function getFilterNamesAsDbColumns()
    {
        $filterArray = get_object_vars($this);
        $filterNames = array_keys(array_filter($filterArray));
        $sqlFragments = [];
        foreach($filterNames as $filter) {
            $filter = strtolower(implode("_", preg_split('/(?=[A-Z])/', $filter)));
            $sqlFragments[] = '' . $filter . '=? ';
        }
        $sql = implode("  AND  ", $sqlFragments);

        return $sql;
    }

    // To Array, remove null values from filter
    public function toArray()
    {
        return array_filter(get_object_vars($this));
    }
}
