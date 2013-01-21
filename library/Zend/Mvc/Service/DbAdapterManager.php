<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Mvc\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Exception\RuntimeException;
use Zend\Mvc\Exception\InvalidArgumentException;

class DbAdapterManager implements ServiceLocatorAwareInterface
{
    /**
     *
     * @var Adapter[]
     */
    protected $_dbAdapter = array();

    protected $_dbAdapterConfig = array();

    /**
     * @var ServiceLocatorInterface
     */
    protected $_serviceLocator;

    /**
     * @param array $config
     * @throws RuntimeException
     */
    public function addDbAdapterConfig(array $configArray)
    {
        foreach ($configArray as $key => $config) {
            if ( $this->hasAdapter($key) ) {
                throw new RuntimeException(sprintf("adapter with key(%s) is allready registered",$key));
            } elseif ( $this->hasAdapterConfig($key) ) {
                throw new RuntimeException(sprintf("adapter config with key(%s) is allready defined",$key));
            }

            $this->_dbAdapterConfig[ $key ] = $config;
        }
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->_serviceLocator = $serviceLocator;
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->_serviceLocator;
    }

    /**
     * @param array $config
     * @throws RuntimeException
     */
    public function hasAdapterConfig($adapterKey)
    {
        return ( isset($this->_dbAdapterConfig[ $adapterKey ]) );
    }

    /**
     * @param string $key
     * @param Adapter $adapter
     * @throws RuntimeException
     * @return boolean
     */
    public function addAdapter($key, Adapter $adapter)
    {
        if ( $this->hasAdapter($key) ) {
            if ( $this->_dbAdapter[$key] === $adapter ) {
                return true;
            }
            throw new RuntimeException(sprintf("adapter key (%s) allready exist",$key));
        }

        $this->_dbAdapter[ $key ] = $adapter;
    }

    /**
     *
     * @param string $key
     * @return bool
     */
    public function hasAdapter($key)
    {
        return ( isset($this->_dbAdapter[$key]) );
    }

    /**
     * @param string $key
     * @throws RuntimeException
     * @return Adapter
     */
    public function getAdapter($key)
    {
        if ( !$this->hasAdapter($key) ) {
            if ( !$this->hasAdapterConfig($key) ) {
                throw new RuntimeException(sprintf("adapter key (%s) not exist",$key));
            }

            $this->initAdapter($key);
            if ( !$this->hasAdapter($key) ) {
                throw new RuntimeException(sprintf("adapter cound init for key (%s)",$key));
            }
        }

        return $this->_dbAdapter[ $key ];
    }

    /**
     * @param string $key
     * @throws RuntimeException
     * @return Adapter
     */
    protected function initAdapter($key)
    {
        $config = $this->_dbAdapterConfig[ $key ];

        if ( is_string($config) ) {
            $this->_dbAdapter[ $key ] = $this->getAdapter($config);
        } elseif (!is_array($config) ||
                  !array_key_exists('driver', $config)
        ) {
            throw new RuntimeException("adapter config on key (%s) is not an valid key or array");
        } else {
            $this->_dbAdapter[ $key ] = $this->adapterFactory( $config, $this->getServiceLocator() );
        }

        return $this->_dbAdapter[ $key ];
    }

    /**
     * @param array $config
     * @param ServiceLocatorInterface $serviceLocator
     * @throws InvalidArgumentException
     * @return Adapter
     */
    public function adapterFactory($config, ServiceLocatorInterface $serviceLocator=null)
    {
        if ( $serviceLocator === null ) {
            $serviceLocator = $this->getServiceLocator();
        }

        $driver               = null;
        $platform             = null;
        $queryResultPrototype = null;

        if ( isset($config['driver']) ) {
            if ( is_array($config['driver']) ) {
                $driver = $config['driver'];
            } elseif ( is_string($config['driver']) ) {
                if( class_exists($config['driver']) ) {
                    $driver = new $config['driver']();
                } else {
                    $driver = $serviceLocator->get($config['driver']);
                }

                if ( !is_object($platform) ) {
                    throw new InvalidArgumentException("database config['driver'] string is not a confirmed class/service name");
                }
            } else {
                throw new InvalidArgumentException("database config['driver'] must be a array or string of class/service name");
            }
        }

        if ( isset($config['platform']) ) {
            if( class_exists($config['platform']) ) {
                $platform = new $config['platform']();
            } else {
                $platform = $serviceLocator->get($config['platform']);
            }
        }

        if ( !is_object($platform) ) {
            $platform = null;
        }

        if ( isset($config['queryResultPrototype']) ) {
            if( class_exists($config['queryResultPrototype']) ) {
                $queryResultPrototype = new $config['queryResultPrototype']();
            } else {
                $queryResultPrototype = $serviceLocator->get($config['queryResultPrototype']);
            }
        }

        if ( !is_object($queryResultPrototype) ) {
            $queryResultPrototype = null;
        }

        return new Adapter($driver,
                           $platform,
                           $queryResultPrototype
                          );
    }
}
