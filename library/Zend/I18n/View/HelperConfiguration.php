<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace Zend\I18n\View;

use Zend\ServiceManager\ConfigurationInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Service manager configuration for i18n view helpers.
 *
 * @category   Zend
 * @package    Zend_I18n
 * @subpackage View
 */
class HelperConfiguration implements ConfigurationInterface
{
    /**
     * @var array Pre-aliased view helpers
     */
    protected $invokables = array(
        'currencyFormat'  => 'Zend\I18n\View\Helper\CurrencyFormat',
        'dateFormat'      => 'Zend\I18n\View\Helper\DateFormat',
        'numberFormat'    => 'Zend\I18n\View\Helper\NumberFormat',
        'translate'       => 'Zend\I18n\View\Helper\Translate',
        'translatePlural' => 'Zend\I18n\View\Helper\TranslatePlural',
    );

    /**
     * Configure the provided service manager instance with the configuration
     * in this class.
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
