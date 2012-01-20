<?php

namespace Zend\Db\Adapter;

interface DriverStatement
{
    /**
     * @param Driver $driver
     */
    public function setDriver(Driver $driver);

    /**
     * @param resource $resource
     */
    public function setResource($resource);

    /**
     * @param string $sql
     */
    public function setSql($sql);

    /**
     * @param DriverStatement\ParameterContainer $parameterContainer
     */
    public function setParameterContainer(DriverStatement\ParameterContainer $parameterContainer);

    /**
     * @return resource
     */
    public function getResource();
    public function getSQL();
    public function isQuery();
    public function execute($parameters = null);
}
