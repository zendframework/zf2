<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

abstract class AbstractFilter implements FilterInterface
{
    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    /**
     * Set options for the given filter
     * 
     * @param array $options
     */
    public function setOptions(array $options)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke($value)
    {
        return $this->filter($value);
    }
}
