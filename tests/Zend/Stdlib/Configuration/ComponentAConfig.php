<?php
namespace ZendTest\Stdlib\Configuration;

use Zend\Stdlib\AbstractConfiguration;

class ComponentAConfig extends AbstractConfiguration
{
	protected $_config;

	public function __construct(ComponentAConfiguration )

    public function getConfig(){
    	return $this->_config;
    }

    public function setConfig(){
    	return $this->_config;
    }

}
