<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\WindowsAzure\Storage;

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage Storage
 */
class DynamicTableEntity extends TableEntity
{
    /**
     * Dynamic properties
     *
     * @var array
     */
    protected $_dynamicProperties = array();

    /**
     * Magic overload for setting properties
     *
     * @param string $name     Name of the property
     * @param string $value    Value to set
     */
    public function __set($name, $value)
    {
        $this->setAzureProperty($name, $value, null);
    }

    /**
     * Magic overload for getting properties
     *
     * @param string $name Name of the property
     * @return DynamicTableEntity
     */
    public function __get($name)
    {
        return $this->getAzureProperty($name);
    }

    /**
     * Set an Azure property
     *
     * @param string $name  Property name
     * @param mixed  $value Property value
     * @param string $type  Property type (Edm.xxxx)
     * @return DynamicTableEntity
     */
    public function setAzureProperty($name, $value = '', $type = null)
    {
        if (strtolower($name) == 'partitionkey') {
            $this->setPartitionKey($value);
        } elseif (strtolower($name) == 'rowkey') {
            $this->setRowKey($value);
        } elseif (strtolower($name) == 'etag') {
            $this->setEtag($value);
        } else {
            if (!array_key_exists(strtolower($name), $this->_dynamicProperties)) {
                // Determine type?
                if ($type === null) {
                    $type = 'Edm.String';
                    if (is_int($value)) {
                        $type = 'Edm.Int32';
                    } elseif (is_float($value)) {
                        $type = 'Edm.Double';
                    } elseif (is_bool($value)) {
                        $type = 'Edm.Boolean';
                    }
                }

                // Set dynamic property
                $this->_dynamicProperties[strtolower($name)] = (object)array(
                    'Name'  => $name,
                    'Type'  => $type,
                    'Value' => $value,
                );
            }

            $this->_dynamicProperties[strtolower($name)]->Value = $value;
        }
        return $this;
    }

    /**
     * Set an Azure property type
     *
     * @param string $name Property name
     * @param string $type Property type (Edm.xxxx)
     * @return DynamicTableEntity
     */
    public function setAzurePropertyType($name, $type = 'Edm.String')
    {
        if (!array_key_exists(strtolower($name), $this->_dynamicProperties)) {
            $this->setAzureProperty($name, '', $type);
        } else {
            $this->_dynamicProperties[strtolower($name)]->Type = $type;
        }
        return $this;
    }

    /**
     * Get an Azure property
     *
     * @param string $name  Property name
     * @return DynamicTableEntity
     */
    public function getAzureProperty($name)
    {
        if (strtolower($name) == 'partitionkey') {
            return $this->getPartitionKey();
        }
        if (strtolower($name) == 'rowkey') {
            return $this->getRowKey();
        }
        if (strtolower($name) == 'etag') {
            return $this->getEtag();
        }

        if (!array_key_exists(strtolower($name), $this->_dynamicProperties)) {
            $this->setAzureProperty($name);
        }

        return $this->_dynamicProperties[strtolower($name)]->Value;
    }

    /**
     * Get an Azure property type
     *
     * @param string $name Property name
     * @return string Property type (Edm.xxxx)
     */
    public function getAzurePropertyType($name)
    {
        if (!array_key_exists(strtolower($name), $this->_dynamicProperties)) {
            $this->setAzureProperty($name, '');
        }

        return $this->_dynamicProperties[strtolower($name)]->Type;
    }

    /**
     * Get Azure values
     *
     * @return array
     */
    public function getAzureValues()
    {
        return array_merge(array_values($this->_dynamicProperties), parent::getAzureValues());
    }

    /**
     * Set Azure values
     *
     * @param array   $values
     * @param boolean $throwOnError Throw Zend_Service_WindowsAzure_Exception when a property is not specified in $values?
     * @throws \Zend\Service\WindowsAzure\Exception\UnknownPropertyException
     */
    public function setAzureValues($values = array(), $throwOnError = false)
    {
        // Set parent values
        parent::setAzureValues($values, false);

        // Set current values
        foreach ($values as $key => $value) {
            $this->$key = $value;
        }
    }
}
