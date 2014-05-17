<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link           http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright      Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license        http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Hydrator\Filter;

use Zend\Hydrator\Context\ExtractionContext;

/**
 * This filter exclude any methods that have a name in the array
 */
class ExcludeMethodsFilter implements FilterInterface
{
    /**
     * The methods to exclude
     *
     * @var array
     */
    protected $methods;

    /**
     * @param array|string $methods The methods to exclude
     */
    public function __construct($methods)
    {
        // Performance optimization, as it allows us to do isset instead of in_array
        $this->methods = array_flip((array) $methods);
    }

    /**
     * {@inheritDoc}
     */
    public function accept($property, ExtractionContext $context = null)
    {
        $pos = strpos($property, '::');
        $pos = $pos !== false ? $pos + 2 : 0;

        return !isset($this->methods[substr($property, $pos)]);
    }
}
