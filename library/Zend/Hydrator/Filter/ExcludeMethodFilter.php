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
 * This filter exclude any method that have the given name
 */
class ExcludeMethodFilter implements FilterInterface
{
    /**
     * The method to exclude
     *
     * @var string
     */
    protected $method;

    /**
     * @param string $method The method to exclude
     */
    public function __construct($method)
    {
        $this->method = (string) $method;
    }

    /**
     * {@inheritDoc}
     */
    public function accept($property, $context = null)
    {
        $pos = strpos($property, '::');
        $pos = $pos !== false ? $pos + 2 : 0;

        return substr($property, $pos) !== $this->method;
    }
}
