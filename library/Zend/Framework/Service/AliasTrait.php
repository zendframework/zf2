<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

trait AliasTrait
{
    /**
     * @var array
     */
    protected $alias = [];

    /**
     * @param $name
     * @return string
     */
    public function alias($name)
    {
        $lowercase = strtolower($name);
        return isset($this->alias[$lowercase]) ? $this->alias[$lowercase] : $name;
    }
}
