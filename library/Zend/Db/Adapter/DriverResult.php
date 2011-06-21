<?php

namespace Zend\Db\Adapter;

use Zend\Db\ResultSet\DataSource;

interface DriverResult extends DataSource
{
    /* public function __construct(Driver $driver, $resource, array $options); */
    public function setDriver(Driver $driver);
    public function setResource($resource);
    public function getResource();
}
