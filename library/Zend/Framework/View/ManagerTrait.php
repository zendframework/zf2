<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

use Zend\Framework\Service\AliasTrait as Alias;

trait ManagerTrait
{
    /**
     *
     */
    use Alias;

    /**
     * @var array
     */
    protected $config;

    /**
     * @return array
     */
    public function viewHelpers()
    {
        return $this->config['view_helpers'];
    }

    /**
     * @return string
     */
    public function layoutTemplate()
    {
        return $this->config['layout_template'];
    }

    /**
     * @return bool
     */
    public function displayExceptions()
    {
        return $this->config['display_exceptions'];
    }

    /**
     * @return bool
     */
    public function displayNotFoundReason()
    {
        return $this->config['display_not_found_reason'];
    }

    /**
     * @return string
     */
    public function exceptionTemplate()
    {
        return $this->config['exception_template'];
    }

    /**
     * @return string
     */
    public function notFoundTemplate()
    {
        return $this->config['not_found_template'];
    }
}
