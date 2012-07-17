<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace Zend\Form\View;

use Zend\ServiceManager\ConfigurationInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Service manager configuration for form view helpers
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 */
class HelperConfiguration implements ConfigurationInterface
{
    /**
     * @var array Pre-aliased view helpers
     */
    protected $invokables = array(
        'form'                   => 'Zend\Form\View\Helper\Form',
        'formButton'             => 'Zend\Form\View\Helper\FormButton',
        'formCaptcha'            => 'Zend\Form\View\Helper\FormCaptcha',
        'captchaDumb'            => 'Zend\Form\View\Helper\Captcha\Dumb',
        'formCaptchaDumb'        => 'Zend\Form\View\Helper\Captcha\Dumb',
        'captchaFiglet'          => 'Zend\Form\View\Helper\Captcha\Figlet',
        'formCaptchaFiglet'      => 'Zend\Form\View\Helper\Captcha\Figlet',
        'captchaImage'           => 'Zend\Form\View\Helper\Captcha\Image',
        'formCaptchaImage'       => 'Zend\Form\View\Helper\Captcha\Image',
        'captchaReCaptcha'       => 'Zend\Form\View\Helper\Captcha\ReCaptcha',
        'formCaptchaReCaptcha'   => 'Zend\Form\View\Helper\Captcha\ReCaptcha',
        'formCheckbox'           => 'Zend\Form\View\Helper\FormCheckbox',
        'formCollection'         => 'Zend\Form\View\Helper\FormCollection',
        'formColor'              => 'Zend\Form\View\Helper\FormColor',
        'formDate'               => 'Zend\Form\View\Helper\FormDate',
        'formDateTime'           => 'Zend\Form\View\Helper\FormDateTime',
        'formDateTimeLocal'      => 'Zend\Form\View\Helper\FormDateTimeLocal',
        'formElement'            => 'Zend\Form\View\Helper\FormElement',
        'formElementErrors'      => 'Zend\Form\View\Helper\FormElementErrors',
        'formEmail'              => 'Zend\Form\View\Helper\FormEmail',
        'formFile'               => 'Zend\Form\View\Helper\FormFile',
        'formHidden'             => 'Zend\Form\View\Helper\FormHidden',
        'formImage'              => 'Zend\Form\View\Helper\FormImage',
        'formInput'              => 'Zend\Form\View\Helper\FormInput',
        'formLabel'              => 'Zend\Form\View\Helper\FormLabel',
        'formMonth'              => 'Zend\Form\View\Helper\FormMonth',
        'formMultiCheckbox'      => 'Zend\Form\View\Helper\FormMultiCheckbox',
        'formNumber'             => 'Zend\Form\View\Helper\FormNumber',
        'formPassword'           => 'Zend\Form\View\Helper\FormPassword',
        'formRadio'              => 'Zend\Form\View\Helper\FormRadio',
        'formRange'              => 'Zend\Form\View\Helper\FormRange',
        'formReset'              => 'Zend\Form\View\Helper\FormReset',
        'formRow'                => 'Zend\Form\View\Helper\FormRow',
        'formSearch'             => 'Zend\Form\View\Helper\FormSearch',
        'formSelect'             => 'Zend\Form\View\Helper\FormSelect',
        'formSubmit'             => 'Zend\Form\View\Helper\FormSubmit',
        'formTel'                => 'Zend\Form\View\Helper\FormTel',
        'formText'               => 'Zend\Form\View\Helper\FormText',
        'formTextarea'           => 'Zend\Form\View\Helper\FormTextarea',
        'formTime'               => 'Zend\Form\View\Helper\FormTime',
        'formUrl'                => 'Zend\Form\View\Helper\FormUrl',
        'formWeek'               => 'Zend\Form\View\Helper\FormWeek',
    );

    /**
     * Configure the provided service manager instance with the configuration
     * in this class.
     *
     * In addition to using each of the internal properties to configure the
     * service manager, also adds an initializer to inject ServiceManagerAware
     * classes with the service manager.
     *
     * @param  ServiceManager $serviceManager
     * @return void
     */
    public function configureServiceManager(ServiceManager $serviceManager)
    {
        foreach ($this->invokables as $name => $service) {
            $serviceManager->setInvokableClass($name, $service);
        }
    }
}
