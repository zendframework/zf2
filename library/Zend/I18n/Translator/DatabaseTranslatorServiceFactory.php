<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace Zend\I18n\Translator;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Translator.
 *
 * @category   Zend
 * @package    Zend_I18n
 * @subpackage Translator
 */
class DatabaseTranslatorServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {

        // Configure the translator
        $config = $serviceLocator->get('Configuration');
        $trConfig = isset($config['translator']) ? $config['translator'] : array();

        if ( isset($trConfig['translation_db']) ) {
            foreach($trConfig['translation_db'] as &$translation_db) {
                if ( is_string($translation_db['dbconnection']) ) {
                    $translation_db['dbconnection'] = $serviceLocator->get($translation_db['dbconnection']);
                }
            }
        }
        $translator = Translator::factory($trConfig);
        return $translator;
    }
}