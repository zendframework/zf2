<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Json
 * @subpackage Server
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Json\Server;
use Zend\Json\Server\Exception\InvalidArgumentException,
    Zend\Json\Server\Exception\RuntimeException;

/**
 * @uses       Zend\Json\Json
 * @uses       Zend\Json\Server\Exception\InvalidArgumentException
 * @uses       Zend\Json\Server\Exception\RuntimeException
 * @uses       Zend\Json\Server\Smd\Service
 * @category   Zend
 * @package    Zend_Json
 * @subpackage Server
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SMD
{
    const ENV_JSONRPC_1 = 'JSON-RPC-1.0';
    const ENV_JSONRPC_2 = 'JSON-RPC-2.0';
    const SMD_VERSION   = '2.0';

    /**
     * Content type
     * @var string
     */
    protected $_contentType = 'application/json';

    /**
     * Content type regex
     * @var string
     */
    protected $_contentTypeRegex = '#[a-z]+/[a-z][a-z-]+#i';

    /**
     * Service description
     * @var string
     */
    protected $_description;

    /**
     * Generate Dojo-compatible SMD
     * @var bool
     */
    protected $_dojoCompatible = false;

    /**
     * Current envelope
     * @var string
     */
    protected $_envelope = self::ENV_JSONRPC_1;

    /**
     * Allowed envelope types
     * @var array
     */
    protected $_envelopeTypes = array(
        self::ENV_JSONRPC_1,
        self::ENV_JSONRPC_2,
    );

    /**
     * Service id
     * @var string
     */
    protected $_id;

    /**
     * Services offerred
     * @var array
     */
    protected $_services = array();

    /**
     * Service target
     * @var string
     */
    protected $_target;

    /**
     * Global transport
     * @var string
     */
    protected $_transport = 'POST';

    /**
     * Allowed transport types
     * @var array
     */
    protected $_transportTypes = array('POST');

    /**
     * Set object state via options
     *
     * @param  array $options
     * @return Zend\Json\Server\Smd
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * Set transport
     *
     * @param  string $transport
     * @return Zend\Json\Server\Smd
     */
    public function setTransport($transport)
    {
        if (!in_array($transport, $this->_transportTypes)) {
            throw new InvalidArgumentException("Invalid transport '{$transport}' specified");
        }
        $this->_transport = $transport;
        return $this;
    }

    /**
     * Get transport
     *
     * @return string
     */
    public function getTransport()
    {
        return $this->_transport;
    }

    /**
     * Set envelope
     *
     * @param  string $envelopeType
     * @return Zend\Json\Server\Smd
     */
    public function setEnvelope($envelopeType)
    {
        if (!in_array($envelopeType, $this->_envelopeTypes)) {
            throw new InvalidArgumentException("Invalid envelope type '{$envelopeType}'");
        }
        $this->_envelope = $envelopeType;
        return $this;
    }

    /**
     * Retrieve envelope
     *
     * @return string
     */
    public function getEnvelope()
    {
        return $this->_envelope;
    }

    // Content-Type of response; default to application/json
    /**
     * Set content type
     *
     * @param  string $type
     * @return Zend\Json\Server\Smd
     */
    public function setContentType($type)
    {
        if (!preg_match($this->_contentTypeRegex, $type)) {
            throw new InvalidArgumentException("Invalid content type '{$type}' specified");
        }
        $this->_contentType = $type;
        return $this;
    }

    /**
     * Retrieve content type
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->_contentType;
    }

    /**
     * Set service target
     *
     * @param  string $target
     * @return Zend\Json\Server\Smd
     */
    public function setTarget($target)
    {
        $this->_target = (string) $target;
        return $this;
    }

    /**
     * Retrieve service target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->_target;
    }

    /**
     * Set service ID
     *
     * @param  string $Id
     * @return Zend\Json\Server\Smd
     */
    public function setId($id)
    {
        $this->_id = (string) $id;
        return $this->_id;
    }

    /**
     * Get service id
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set service description
     *
     * @param  string $description
     * @return Zend\Json\Server\Smd
     */
    public function setDescription($description)
    {
        $this->_description = (string) $description;
        return $this->_description;
    }

    /**
     * Get service description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Indicate whether or not to generate Dojo-compatible SMD
     *
     * @param  bool $flag
     * @return Zend\Json\Server\Smd
     */
    public function setDojoCompatible($flag)
    {
        $this->_dojoCompatible = (bool) $flag;
        return $this;
    }

    /**
     * Is this a Dojo compatible SMD?
     *
     * @return bool
     */
    public function isDojoCompatible()
    {
        return $this->_dojoCompatible;
    }

    /**
     * Add Service
     *
     * @param Zend\Json\Server\Smd\Service|array $service
     * @return void
     */
    public function addService($service)
    {
        if ($service instanceof Smd\Service) {
            $name = $service->getName();
        } elseif (is_array($service)) {
            $service = new Smd\Service($service);
            $name = $service->getName();
        } else {
            throw new InvalidArgumentException('Invalid service passed to addService()');
        }

        if (array_key_exists($name, $this->_services)) {
            throw new RuntimeException('Attempt to register a service already registered detected');
        }
        $this->_services[$name] = $service;
        return $this;
    }

    /**
     * Add many services
     *
     * @param  array $services
     * @return Zend\Json\Server\Smd
     */
    public function addServices(array $services)
    {
        foreach ($services as $service) {
            $this->addService($service);
        }
        return $this;
    }

    /**
     * Overwrite existing services with new ones
     *
     * @param  array $services
     * @return Zend\Json\Server\Smd
     */
    public function setServices(array $services)
    {
        $this->_services = array();
        return $this->addServices($services);
    }

    /**
     * Get service object
     *
     * @param  string $name
     * @return false|Zend\Json\Server\Smd\Service
     */
    public function getService($name)
    {
        if (array_key_exists($name, $this->_services)) {
            return $this->_services[$name];
        }
        return false;
    }

    /**
     * Return services
     *
     * @return array
     */
    public function getServices()
    {
        return $this->_services;
    }

    /**
     * Remove service
     *
     * @param  string $name
     * @return boolean
     */
    public function removeService($name)
    {
        if (array_key_exists($name, $this->_services)) {
            unset($this->_services[$name]);
            return true;
        }
        return false;
    }

    /**
     * Cast to array
     *
     * @return array
     */
    public function toArray()
    {
        if ($this->isDojoCompatible()) {
            return $this->toDojoArray();
        }

        $transport   = $this->getTransport();
        $envelope    = $this->getEnvelope();
        $contentType = $this->getContentType();
        $SMDVersion  = self::SMD_VERSION;
        $service = compact('transport', 'envelope', 'contentType', 'SMDVersion');

        if (null !== ($target = $this->getTarget())) {
            $service['target']     = $target;
        }
        if (null !== ($id = $this->getId())) {
            $service['id'] = $id;
        }

        $services = $this->getServices();
        if (!empty($services)) {
            $service['services'] = array();
            foreach ($services as $name => $svc) {
                $svc->setEnvelope($envelope);
                $service['services'][$name] = $svc->toArray();
            }
            $service['methods'] = $service['services'];
        }

        return $service;
    }

    /**
     * Export to DOJO-compatible SMD array
     *
     * @return array
     */
    public function toDojoArray()
    {
        $SMDVersion  = '.1';
        $serviceType = 'JSON-RPC';
        $service = compact('SMDVersion', 'serviceType');

        $target = $this->getTarget();

        $services = $this->getServices();
        if (!empty($services)) {
            $service['methods'] = array();
            foreach ($services as $name => $svc) {
                $method = array(
                    'name'       => $name,
                    'serviceURL' => $target,
                );
                $params = array();
                foreach ($svc->getParams() as $param) {
                    $paramName = array_key_exists('name', $param) ? $param['name'] : $param['type'];
                    $params[] = array(
                        'name' => $paramName,
                        'type' => $param['type'],
                    );
                }
                if (!empty($params)) {
                    $method['parameters'] = $params;
                }
                $service['methods'][] = $method;
            }
        }

        return $service;
    }

    /**
     * Cast to JSON
     *
     * @return string
     */
    public function toJson()
    {
        return \Zend\Json\Json::encode($this->toArray());
    }

    /**
     * Cast to string (JSON)
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}

