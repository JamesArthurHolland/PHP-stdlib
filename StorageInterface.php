<?php

namespace AE\Stdlib;

interface StorageInterface
{
    public function save($entity);
    public function fetch($id);
}