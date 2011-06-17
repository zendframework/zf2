<?php

namespace Zend\Db\Adapter;

interface Driver
{
    const NAME_FORMAT_CAMELCASE = 'camelCase';
    const NAME_FORMAT_NATURAL = 'natural';

    public function getDatabasePlatformName($nameFormat = self::NAME_FORMAT_CAMELCASE);
    public function checkEnvironment();
    
    public function getConnectionClass();
    public function getConnectionParams();
    public function getStatementClass();
    public function getStatementParams();
    public function getResultClass();
    public function getResultParams();
    public function getPrepareTypeSupport();
    public function formatNamedParameter($name);
    
    /**
     * @return Zend\Db\Adapter\DriverConnection
     */
    public function getConnection();

}
