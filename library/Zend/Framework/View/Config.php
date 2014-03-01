<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

use Zend\Framework\Config\ConfigTrait as ConfigTrait;

class Config
    implements ConfigInterface
{
    /**
     *
     */
    use ConfigTrait;

    /**
     * @return array
     */
    public function aliases()
    {
        return $this->configured('view_helpers');
    }

    /**
     * @return string
     */
    public function basePath()
    {
        return $this->configured('base_path');
    }

    /**
     * @return string
     */
    public function defaultTemplateSuffix()
    {
        return $this->configured('default_template_suffix');
    }

    /**
     * @return bool
     */
    public function displayExceptions()
    {
        return $this->configured('display_exceptions');
    }

    /**
     * @return bool
     */
    public function displayNotFoundReason()
    {
        return $this->configured('display_not_found_reason');
    }

    /**
     * @return string
     */
    public function docType()
    {
        return $this->configured('doctype');
    }

    /**
     * @return string
     */
    public function exceptionTemplate()
    {
        return $this->configured('exception_template');
    }

    /**
     * @return string
     */
    public function layoutTemplate()
    {
        return $this->configured('layout_template');
    }

    /**
     * @return string
     */
    public function notFoundTemplate()
    {
        return $this->configured('not_found_template');
    }

    /**
     * @return array
     */
    public function templateMap()
    {
        return (array) $this->configured('template_map');
    }

    /**
     * @return array
     */
    public function templatePathStack()
    {
        return (array) $this->configured('template_path_stack');
    }
}
