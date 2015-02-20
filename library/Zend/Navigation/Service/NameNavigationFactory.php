<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Navigation\Service;

/**
 * Name navigation factory.
 *
 * Creates navigation depending on given config key name
 */
class NameNavigationFactory extends AbstractNavigationFactory
{
    /**
     * Config name
     *
     * @var string
     */
    protected $name;

    /**
     * Initialize object
     *
     * @param string $name Config name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Returns name
     *
     * @return string
     */
    protected function getName()
    {
        return $this->name;
    }
}
