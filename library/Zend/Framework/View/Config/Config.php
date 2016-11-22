<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Config;

use Zend\Framework\Config\ConfigTrait;

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
        return $this->get('view_helpers');
    }

    /**
     * @return string
     */
    public function basePath()
    {
        return $this->get('base_path');
    }

    /**
     * @return string
     */
    public function defaultTemplateSuffix()
    {
        return $this->get('default_template_suffix');
    }

    /**
     * @return bool
     */
    public function displayExceptions()
    {
        return $this->get('display_exceptions');
    }

    /**
     * @return bool
     */
    public function displayNotFoundReason()
    {
        return $this->get('display_not_found_reason');
    }

    /**
     * @return string
     */
    public function docType()
    {
        return $this->get('doctype');
    }

    /**
     * @return string
     */
    public function exceptionTemplate()
    {
        return $this->get('exception_template');
    }

    /**
     * @return string
     */
    public function layoutTemplate()
    {
        return $this->get('layout_template');
    }

    /**
     * @return string
     */
    public function notFoundTemplate()
    {
        return $this->get('not_found_template');
    }

    /**
     * @return array
     */
    public function templateMap()
    {
        return $this->get('template_map');
    }

    /**
     * @return array
     */
    public function templatePathStack()
    {
        return $this->get('template_path_stack');
    }
}
