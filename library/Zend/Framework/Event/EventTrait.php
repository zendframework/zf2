<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event;

trait EventTrait
{
    /**
     * Name
     *
     * @return string|array
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Name set
     *
     * @param $name string|array
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}
