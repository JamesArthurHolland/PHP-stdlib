<?php

namespace AE\Stdlib;

class FilterMapperAbstract
{
    protected $filter;

    public function __construct($filter)
    {
        $this->setFilter($filter);
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        return $this;
    }

    public function fromArray($filterArray)
    {
        $filter = $this->getFilter();

        foreach($filterArray as $key => $array) {
            $setter = 'set' . ucwords($key);
            $filter->$setter($array);
        }

        return $filter;
    }
}