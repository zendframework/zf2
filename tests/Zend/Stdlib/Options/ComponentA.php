<?php
namespace ZendTest\Stdlib\Options;

use Zend\Stdlib\Configurable,
	Zend\Stdlib\Options;

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
	 * @param \Options\Stdlib\Options $config
	 * @return void
	 */
    public function setConfig(Options $config){
    	$this->_config = $config;
    }

}
