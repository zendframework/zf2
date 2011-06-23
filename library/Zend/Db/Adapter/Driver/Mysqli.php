<?php

namespace Zend\Db\Adapter\Driver;

class Mysqli extends AbstractDriver
{
    protected $connectionClass = 'Zend\Db\Adapter\Driver\Mysqli\Connection';
    protected $statementClass = 'Zend\Db\Adapter\Driver\Mysqli\Statement';
    protected $resultClass = 'Zend\Db\Adapter\Driver\Mysqli\Result';
    
    public function getDatabasePlatformName($nameFormat = self::NAME_FORMAT_CAMELCASE)
    {
        if ($nameFormat == self::NAME_FORMAT_CAMELCASE) {
            return 'Mysql';
        } else {
            return 'MySQL';
        }
    }
    
    public function checkEnvironment()
    {
        if (!extension_loaded('mysqli')) {
            throw new \Exception('The Mysqli extension is required for this adapter but the extension is not loaded');
        }
    }
    
}
