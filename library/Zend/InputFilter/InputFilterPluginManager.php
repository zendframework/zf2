<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;

/**
 * Input filter plugin manager
 */
class InputFilterPluginManager extends AbstractPluginManager
{
    /**
     * Default set of plugins
     *
     * @var array
     */
    protected $factories = array(
        'Zend\InputFilter\FileInput'       => 'Zend\InputFilter\Factory\FileInputFactory',
        'Zend\InputFilter\Input'           => 'Zend\InputFilter\Factory\InputFactory',
        'Zend\InputFilter\InputCollection' => 'Zend\InputFilter\Factory\InputCollectionFactory'
    );

    /**
     * @var array
     */
    protected $aliases = array(
        'fileinput'       => 'Zend\InputFilter\FileInput',
        'input'           => 'Zend\InputFilter\Input',
        'inputcollection' => 'Zend\InputFilter\InputCollection'
    );

    /**
     * Whether or not to share by default
     *
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * @param  mixed $plugin
     * @return InputInterface|InputCollectionInterface
     * @throws Exception\RuntimeException
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof InputInterface || $plugin instanceof InputCollectionInterface) {
            // we're okay
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement Zend\InputFilter\InputInterface or Zend\InputFilter\InputCollectionInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
