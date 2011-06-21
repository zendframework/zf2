<?php

namespace Zend\Db\Adapter\Driver;

class Sqlsrv extends \Zend\Db\Adapter\AbstractDriver
{
    protected $connectionClass = 'Zend\Db\Adapter\Driver\Sqlsrv\Connection';
    protected $statementClass = 'Zend\Db\Adapter\Driver\Sqlsrv\Statement';
    protected $resultClass = 'Zend\Db\Adapter\Driver\Sqlsrv\Result';
    
    public function getDatabasePlatformName($nameFormat = self::NAME_FORMAT_CAMELCASE)
    {
        if ($nameFormat == self::NAME_FORMAT_CAMELCASE) {
            return 'Sqlsrv';
        } else {
            return 'SQLServer';
        }
    }
    
    public function checkEnvironment()
    {
        if (!extension_loaded('sqlsrv')) {
            throw new \Exception('The Sqlsrv extension is required for this adapter but the extension is not loaded');
        }
    }
}
