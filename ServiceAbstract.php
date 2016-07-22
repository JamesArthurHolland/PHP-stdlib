<?php
/**
 * Created by PhpStorm.
 * User: jamie
 * Date: 06/10/14
 * Time: 20:46
 */

namespace AE\Stdlib;


abstract class ServiceAbstract
{
    protected $repository;

    public function fetch($id)
    {
//        var_dump($this->getRepository()->fetch($id));
//        die("service abstract");
        return $this->getRepository()->fetch($id);
    }

    public function save($entity)
    {
        return $this->getRepository()->save($entity);
    }

    public function delete($id)
    {
        $this->getRepository()->delete($id);
    }

    public function fetchCollection($ids, $page = 1, $limit = 10)
    {
        return $this->getRepository()->fetchCollection($ids, $page, $limit);
    }

    public function fetchCollectionPaginationFilters($filter, $page = 1, $limit = 10)
    {
        return $this->getRepository()->fetchCollectionPaginationFilters($filter, $page, $limit);
    }

    protected function getRepository()
    {
        return $this->repository;
    }

    protected function setRepository($repository)
    {
        $this->repository = $repository;
        return $this;
    }
}