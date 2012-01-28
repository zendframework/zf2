<?php

namespace Zend\Db\Adapter;

interface DriverResult extends \Countable, \Traversable
{
    public function isQueryResult();
    public function getAffectedRows();
    public function getResource();
}
