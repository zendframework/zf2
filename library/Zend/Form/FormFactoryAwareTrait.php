<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      https://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form;

use \Zend\Form\Factory;

trait FormFactoryAwareTrait
{
    /**
     * @var Factory
     */
    protected $factory = null;

    /**
     * Compose a form factory into the object
     *
     * @param Factory $factory
     * @return mixed
     */
    public function setFormFactory(Factory $factory)
    {
        $this->factory = $factory;

        return $this;
    }
}
