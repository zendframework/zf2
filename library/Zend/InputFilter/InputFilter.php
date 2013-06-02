<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

/**
 * Input filter
 */
class InputFilter extends BaseInputFilter
{
    /**
     * @var InputFilterFactory
     */
    protected $factory;

    /**
     * @param InputFilterFactory $factory
     */
    public function __construct(InputFilterFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Get the input filter factory
     *
     * @return InputFilterFactory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Add an input or input filter through a specification
     *
     * @param array|InputFilterInterface|InputInterface $specification
     * @param string|null                               $name
     */
    public function add($specification, $name = null)
    {
        if ($specification instanceof InputInterface || $specification instanceof InputFilterInterface) {
            parent::add($specification, $name);
        }

        $inputOrInputFilter = $this->factory->createFromSpecification($specification);

        parent::add($inputOrInputFilter, $name);
    }
}
