<?php

namespace AE\Stdlib;

trait EntityTrait
{
    public function __construct()
    {
    }

    public function toValues()
    {
        $filterArray = array_filter(get_object_vars($this));
        return array_values($filterArray);
    }

    public function jsonSerialize()
    {
        $objectArray = [];
        foreach($this as $key => $value) {
            $objectArray[$key] = $value;
        }
        return $objectArray;
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

    // To Array, remove null values from filter
    public function toArray()
    {
        $objectAsArray = [];
        foreach(get_object_vars($this) as $key => $value) {
            $objectAsArray[$key] = $value;
        }
        return $objectAsArray;
    }

}

