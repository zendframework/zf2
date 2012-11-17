<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace Zend\ServiceManager\Proxy;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;

use ReflectionClass;

use Zend\ServiceManager\Exception;

/**
 * Class metadata for a generic service object
 *
 * @category Zend
 * @package  Zend_ServiceManager
 * @author   Marco Pivetta <ocramius@gmail.com>
 */
class ServiceClassMetadata implements ClassMetadata
{
    /**
     * @var ReflectionClass
     */
    protected $reflectionClass;

    /**
     * @param object|string $service
     */
    public function __construct($service)
    {
        $this->reflectionClass = new ReflectionClass($service);
    }

    /**
     * {@inheritDoc}
     */
    function getName()
    {
        return $this->reflectionClass->getName();
    }

    /**
     * {@inheritDoc}
     */
    function getIdentifier()
    {
        return array();
    }

    /**
     * {@inheritDoc}
     */
    function getReflectionClass()
    {
        return $this->reflectionClass;
    }

    /**
     * {@inheritDoc}
     */
    function isIdentifier($fieldName)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    function hasField($fieldName)
    {
        return $this->reflectionClass->hasProperty($fieldName);
    }

    /**
     * {@inheritDoc}
     */
    function hasAssociation($fieldName)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    function isSingleValuedAssociation($fieldName)
    {
        throw new Exception\RuntimeException('Not implemented');
    }

    /**
     * {@inheritDoc}
     */
    function isCollectionValuedAssociation($fieldName)
    {
        throw new Exception\RuntimeException('Not implemented');
    }

    /**
     * {@inheritDoc}
     */
    function getFieldNames()
    {
        $properties = $this->reflectionClass->getProperties();
        $fields     = array();

        foreach ($properties as $property) {
            $fields[] = $property->getName();
        }

        return $fields;
    }

    /**
     * {@inheritDoc}
     */
    function getIdentifierFieldNames()
    {
        return array();
    }

    /**
     * {@inheritDoc}
     */
    function getAssociationNames()
    {
        return array();
    }

    /**
     * {@inheritDoc}
     */
    function getTypeOfField($fieldName)
    {
        throw new Exception\RuntimeException('Not implemented');
    }

    /**
     * {@inheritDoc}
     */
    function getAssociationTargetClass($assocName)
    {
        throw new Exception\RuntimeException('Not implemented');
    }

    /**
     * {@inheritDoc}
     */
    function isAssociationInverseSide($assocName)
    {
        throw new Exception\RuntimeException('Not implemented');
    }

    /**
     * {@inheritDoc}
     */
    function getAssociationMappedByTargetField($assocName)
    {
        throw new Exception\RuntimeException('Not implemented');
    }

    /**
     * {@inheritDoc}
     */
    function getIdentifierValues($object)
    {
        throw new Exception\RuntimeException('Not implemented');
    }
}