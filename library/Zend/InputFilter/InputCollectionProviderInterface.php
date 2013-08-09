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
 * Input collection provider interface
 */
interface InputCollectionProviderInterface
{
    /**
     * Should return an array specification compatible with {@link Zend\InputFilter\Factory::createInputCollection()}
     *
     * @return array
     */
    public function getInputCollectionSpecification();
}
