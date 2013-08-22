<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link           http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright      Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license        http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Hydrator\Filter;

/**
 * This filter evaluates to false if the given method name matches
 */
class MethodMatchFilter implements FilterInterface
{
    /**
     * The method to exclude
     *
     * @var string
     */
    protected $method;

    /**
     * Either an exclude or an include
     *
     * @var bool
     */
    protected $exclude;

    /**
     * @param string $method  The method to exclude or include
     * @param bool   $exclude If the method should be excluded
     */
    public function __construct($method, $exclude = true)
    {
        $this->method  = (string) $method;
        $this->exclude = (bool) $exclude;
    }

    /**
     * {@inheritDoc}
     */
    public function filter($property, $context = null)
    {
        $pos = strpos($property, '::');
        $pos = $pos !== false ? $pos + 2 : 0;

        return substr($property, $pos) === $this->method && $this->exclude;
    }
}
