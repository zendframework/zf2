<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

trait ConfigTrait
{
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
    public function layoutTemplate()
    {
        return $this->get('layout_template');
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
    public function exceptionTemplate()
    {
        return $this->get('exception_template');
    }

    /**
     * @return string
     */
    public function notFoundTemplate()
    {
        return $this->get('not_found_template');
    }
}
