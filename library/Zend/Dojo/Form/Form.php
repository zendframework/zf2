<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dojo
 */

namespace Zend\Dojo\Form;

use Zend\View\Renderer\RendererInterface as View;

/**
 * Dijit-enabled Form
 *
 * @package    Zend_Dojo
 * @subpackage Form
 */
class Form extends \Zend\Form\Form
{
    /**
     * Constructor
     *
     * @param  array|\Traversable $options
     */
    public function __construct($options = null)
    {
        $this->addPrefixPath('Zend\Dojo\Form\Decorator', 'Zend/Dojo/Form/Decorator', 'decorator')
             ->addPrefixPath('Zend\Dojo\Form\Element', 'Zend/Dojo/Form/Element', 'element')
             ->addElementPrefixPath('Zend\Dojo\Form\Decorator', 'Zend/Dojo/Form/Decorator', 'decorator')
             ->addDisplayGroupPrefixPath('Zend\Dojo\Form\Decorator', 'Zend/Dojo/Form/Decorator')
             ->setDefaultDisplayGroupClass('Zend\Dojo\Form\DisplayGroup');
        parent::__construct($options);
    }

    /**
     * Load the default decorators
     *
     * @return void
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('FormElements')
                 ->addDecorator('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form_dojo'))
                 ->addDecorator('DijitForm');
        }
    }

    /**
     * Set the view object
     *
     * Ensures that the view object has the dojo view helper path set.
     *
     * @param  View $view
     * @return \Zend\Dojo\Form\Element\Dijit
     */
    public function setView(View $view = null)
    {
        if (null !== $view) {
            if(false === $view->getBroker()->isLoaded('dojo')) {
                $loader = new \Zend\Dojo\View\HelperLoader();
                $view->getBroker()->getClassLoader()->registerPlugins($loader);
            }
        }
        return parent::setView($view);
    }
}
