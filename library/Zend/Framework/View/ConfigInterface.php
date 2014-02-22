<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

interface ConfigInterface
{

    /**
     * @param string $name
     * @param mixed $service
     * @return self
     */
    public function add($name, $service);

    /**
     * @return array
     */
    public function aliases();

    /**
     * @return string
     */
    public function defaultTemplateSuffix();

    /**
     * @return bool
     */
    public function displayExceptions();

    /**
     * @return bool
     */
    public function displayNotFoundReason();

    /**
     * @return string
     */
    public function exceptionTemplate();

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name);

    /**
     * @param $name
     * @return bool
     */
    public function has($name);

    /**
     * @return string
     */
    public function layoutTemplate();

    /**
     * @return string
     */
    public function notFoundTemplate();

    /**
     * @return array
     */
    public function templateMap();

    /**
     * @return array
     */
    public function templatePathStack();
}
