<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager;

interface ServiceRequestInterface
{
    /**
     * Retrieve the requested service name
     *
     * @return string
     */
    public function getName();

    /**
     * Set the requested service name
     *
     * @param string $name
     *
     * @return void
     */
    public function setName($name);

    /**
     * Retrieve the requested service instantiation options
     *
     * @return mixed
     */
    public function getOptions();

    /**
     * Set the requested service instantiation options
     *
     * @param mixed $options
     *
     * @return void
     */
    public function setOptions($options);

    /**
     * {@see ServiceRequestInterface::getName()}
     *
     * @return string
     */
    public function __toString();
} 