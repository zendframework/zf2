<?php

namespace Zend\Db\Adapter\Driver;


class Pdo extends AbstractDriver
{
    protected $connectionClass = 'Zend\Db\Adapter\Driver\Pdo\Connection';
    protected $statementClass  = 'Zend\Db\Adapter\Driver\Pdo\Statement';
    protected $resultClass     = 'Zend\Db\Adapter\Driver\Pdo\Result';
    
    public function getDatabasePlatformName($nameFormat = self::NAME_FORMAT_CAMELCASE)
    {
        var_dump($this->getConnection());


//        if ($nameFormat == self::NAME_FORMAT_CAMELCASE) {
//            return 'Pdo';
//        } else {
//            return 'PDO';
//        }
    }
    
    public function checkEnvironment()
    {
        if (!extension_loaded('PDO')) {
            throw new \Exception('The PDO extension is required for this adapter but the extension is not loaded');
        }
    }
    
}
