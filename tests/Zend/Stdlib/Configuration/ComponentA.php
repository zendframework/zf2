<?php
namespace ZendTest\Stdlib\Configuration;

use Zend\Stdlib\Configurable,
	Zend\Stdlib\Configuration;

class ComponentA implements Configurable
{
	/**
	 * @var Configuration
	 */
	protected $_config;

	/**
	 * @param ComponentAConfig $config
	 */
	public function __construct(ComponentAConfig $config){
		$this->setConfig($config);
	}

    /**
	 * @return Configuration
	 */
    public function getConfig(){
    	return $this->_config;
    }

    /**
	 * @param \Zend\Stdlib\Configuration $config
	 * @return void
	 */
    public function setConfig(Configuration $config){
    	$this->_config = $config;
    }

}
