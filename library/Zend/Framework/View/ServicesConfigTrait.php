<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

trait ServicesConfigTrait
{
    /**
     * @return array
     */
    public function aliases()
    {
        return $this->viewConfig()->aliases();
    }

    /**
     * @return string
     */
    public function layoutTemplate()
    {
        return $this->viewConfig()->layoutTemplate();
    }

    /**
     * @return string
     */
    public function defaultTemplateSuffix()
    {
        return $this->viewConfig()->defaultTemplateSuffix();
    }

    /**
     * @return bool
     */
    public function displayExceptions()
    {
        return $this->viewConfig()->displayExceptions();
    }

    /**
     * @return bool
     */
    public function displayNotFoundReason()
    {
        return $this->viewConfig()->displayNotFoundReason();
    }

    /**
     * @return string
     */
    public function exceptionTemplate()
    {
        return $this->viewConfig()->exceptionTemplate();
    }

    /**
     * @return string
     */
    public function notFoundTemplate()
    {
        return $this->viewConfig()->notFoundTemplate();
    }

    /**
     * @return mixed
     */
    public function templateMap()
    {
        return $this->viewConfig()->templateMap();
    }

    /**
     * @return array
     */
    public function templatePathStack()
    {
        return $this->viewConfig()->templatePathStack();
    }

    /**
     * @return ConfigInterface
     */
    public function viewConfig()
    {
        return $this->sm->get('Config')->view();
    }
}
