<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace Zend\I18n\Translator\Loader;

/**
 * Loader interface.
 *
 * @category   Zend
 * @package    Zend_I18n
 * @subpackage Translator
 */
interface LoaderInterface
{
    /**
     * Load translations from a file.
     *
     * @param  string $filename
     * @param  string $locale
     * @return \Zend\I18n\Translator\TextDomain|null
     */
    public function load($filename, $locale);
}
