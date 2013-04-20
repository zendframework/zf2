<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Zend\Stdlib\InitializableInterface;

/**
 * Plugin manager implementation for form elements.
 *
 * Enforces that elements retrieved are instances of ElementInterface.
 */
class FormElementManager extends AbstractPluginManager
{
    /**
     * Default set of helpers
     *
     * @var array
     */
    protected $invokableClasses = array(
        'button'        => 'Zend\Form\Element\Button',
        'captcha'       => 'Zend\Form\Element\Captcha',
        'checkbox'      => 'Zend\Form\Element\Checkbox',
        'collection'    => 'Zend\Form\Element\Collection',
        'color'         => 'Zend\Form\Element\Color',
        'csrf'          => 'Zend\Form\Element\Csrf',
        'date'          => 'Zend\Form\Element\Date',
        'dateselect'    => 'Zend\Form\Element\DateSelect',
        'datetime'      => 'Zend\Form\Element\DateTime',
        'datetimelocal' => 'Zend\Form\Element\DateTimeLocal',
        'datetimeselect' => 'Zend\Form\Element\DateTimeSelect',
        'element'       => 'Zend\Form\Element',
        'email'         => 'Zend\Form\Element\Email',
        'fieldset'      => 'Zend\Form\Fieldset',
        'file'          => 'Zend\Form\Element\File',
        'form'          => 'Zend\Form\Form',
        'hidden'        => 'Zend\Form\Element\Hidden',
        'image'         => 'Zend\Form\Element\Image',
        'month'         => 'Zend\Form\Element\Month',
        'monthselect'   => 'Zend\Form\Element\MonthSelect',
        'multicheckbox' => 'Zend\Form\Element\MultiCheckbox',
        'number'        => 'Zend\Form\Element\Number',
        'password'      => 'Zend\Form\Element\Password',
        'radio'         => 'Zend\Form\Element\Radio',
        'range'         => 'Zend\Form\Element\Range',
        'select'        => 'Zend\Form\Element\Select',
        'submit'        => 'Zend\Form\Element\Submit',
        'text'          => 'Zend\Form\Element\Text',
        'textarea'      => 'Zend\Form\Element\Textarea',
        'time'          => 'Zend\Form\Element\Time',
        'url'           => 'Zend\Form\Element\Url',
        'week'          => 'Zend\Form\Element\Week',
    );

    /**
     * Don't share form elements by default
     *
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * @param ConfigInterface $configuration
     */
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);

        // Form Manager must be injected first so when calling $element->getFormManager()->getFormFactory()
        // returns the same instance as $element->getFormFactory();
        $this->addInitializer(array($this, 'injectFactory'), false);
        $this->addInitializer(array($this, 'injectFormManager'));
    }

    /**
     * Inject the factory to any element that implements <i>FormFactoryAwareInterface</i>
     *
     * @deprecated As of 2.2.0 <b>Form Factory</b> should be retrieved via <b>Form Manager</b>
     * @param ElementInterface $element
     */
    public function injectFactory($element)
    {
        if ($element instanceof FormFactoryAwareInterface) {
            $element->getFormFactory()->setFormElementManager($this);
        }
    }

    /**
     * Inject the <b>Form Manager</b> to any element that implements <i>FormManagerAwareInterface</i>
     *
     * @param ElementInterface $element
     */
    public function injectFormManager($element)
    {
        if (
            $element instanceof FormManagerAwareInterface
            && $this->getServiceLocator() !== null
            && $this->getServiceLocator()->has('FormManager')
        ) {
            $formManager = $this->getServiceLocator()->get('FormManager');
            $formManager->setFormElementManager($this);
            $element->setFormManager($formManager);
        }
    }

    /**
     * Validate the plugin
     *
     * Checks that the element is an instance of ElementInterface
     *
     * @param  mixed $plugin
     * @throws Exception\InvalidElementException
     * @return void
     */
    public function validatePlugin($plugin)
    {
        // Hook to perform various initialization, when the element is not created through the factory
        if ($plugin instanceof InitializableInterface) {
            $plugin->init();
        }

        if ($plugin instanceof ElementInterface) {
            return; // we're okay
        }

        throw new Exception\InvalidElementException(sprintf(
            'Plugin of type %s is invalid; must implement Zend\Form\ElementInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
