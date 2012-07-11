<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ModuleManager
 */

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
