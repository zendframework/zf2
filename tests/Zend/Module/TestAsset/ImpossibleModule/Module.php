<?php

namespace ImpossibleModule;

use Zend\Config\Config;

class Module
{
	protected $version = 1;
	
    public function init()
    {
        $this->initAutoloader();
    }

    protected function initAutoloader()
    {
        include __DIR__ . '/autoload_register.php';
    }

    public function getConfig()
    {
        return new Config(include __DIR__ . '/configs/config.php');
    }
    
	public function getProvides()
    {
    	return array(
    		__NAMESPACE__ => array(
    	 		'version' => $this->version,
    		),
    	);
    }
    
    public function getDependencies()
    {
    	return array(
			'php' => array(
    			'version' => '99.3.0',
    		),
    		'BarModule' => true, 	
    	);
    }
}
