<?php

namespace Zend\Db\Adapter;

use Zend\Db\ResultSet\DataSource as ResultSetDataSource;

interface DriverResult extends ResultSetDataSource\DataSourceInterface
{
    public function __construct(Driver $driver, $resource, array $options);
    public function getResource();
}
