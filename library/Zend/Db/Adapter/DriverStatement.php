<?php

namespace Zend\Db\Adapter;

interface DriverStatement
{
    /* public function __construct(Driver $driver, $resource, $sql); */
    
    public function setDriver(Driver $driver);
    public function setResource($resource);
    public function setSql($sql);
    
    public function getResource();
    public function getSQL();
    public function isQuery();
    public function execute($parameters = null);
}
