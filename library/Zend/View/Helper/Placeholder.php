<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Helper;

use Zend\View\Exception\InvalidArgumentException;

/**
 * Helper for passing data between otherwise segregated Views. It's called
 * Placeholder to make its typical usage obvious, but can be used just as easily
 * for non-Placeholder things. That said, the support for this is only
 * guaranteed to effect subsequently rendered templates, and of course Layouts.
 */
class Placeholder extends AbstractHelper
{
    /**
     * Placeholder items
     *
     * @var array
     */
    protected $items = array();

    /**
     * Placeholder registry intance
     *
     * @var Placeholder\Registry
     */
    protected $registry;

    /**
     * Constructor
     *
     * Retrieve container registry from Placeholder\Registry, or create new one and register it.
     */
    public function __construct()
    {
        $this->registry = Placeholder\Registry::getRegistry();
    }

    /**
     * Placeholder helper
     *
     * @param  string $name
     * @throws InvalidArgumentException
     * @return Placeholder\Container\AbstractContainer
     */
    public function __invoke($name = null)
    {
        if ($name == null) {
            throw new InvalidArgumentException('Placeholder: missing argument.  $name is required by placeholder($name)');
        }

        return $this->getRegistry()->getContainer((string) $name);
    }

    /**
     * Retrieve the registry
     *
     * @return Placeholder\Registry
     */
    public function getRegistry()
    {
        return $this->registry;
    }
}
