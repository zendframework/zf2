<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace Zend\Filter;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager implementation for the filter chain.
 *
 * Enforces that filters retrieved are either callbacks or instances of
 * FilterInterface. Additionally, it registers a number of default filters
 * available, as well as aliases for them.
 *
 * @category   Zend
 * @package    Zend_Filter
 */
class FilterPluginManager extends AbstractPluginManager
{
    /**
     * Default set of filters
     *
     * @var array
     */
    protected $invokableClasses = array(
        'alnum'                     => 'Zend\I18n\Filter\Alnum',
        'alpha'                     => 'Zend\I18n\Filter\Alpha',
        'baseName'                  => 'Zend\Filter\BaseName',
        'boolean'                   => 'Zend\Filter\Boolean',
        'callback'                  => 'Zend\Filter\Callback',
        'compress'                  => 'Zend\Filter\Compress',
        'compressBz2'               => 'Zend\Filter\Compress\Bz2',
        'compressGz'                => 'Zend\Filter\Compress\Gz',
        'compressZzf'               => 'Zend\Filter\Compress\Lzf',
        'compressRar'               => 'Zend\Filter\Compress\Rar',
        'compressTar'               => 'Zend\Filter\Compress\Tar',
        'compressZip'               => 'Zend\Filter\Compress\Zip',
        'decompress'                => 'Zend\Filter\Decompress',
        'decrypt'                   => 'Zend\Filter\Decrypt',
        'digits'                    => 'Zend\Filter\Digits',
        'dir'                       => 'Zend\Filter\Dir',
        'encrypt'                   => 'Zend\Filter\Encrypt',
        'encryptMcrypt'             => 'Zend\Filter\Encrypt\Mcrypt',
        'encryptOpenssl'            => 'Zend\Filter\Encrypt\Openssl',
        'fileDecrypt'               => 'Zend\Filter\File\Decrypt',
        'fileEncrypt'               => 'Zend\Filter\File\Encrypt',
        'fileLowercase'             => 'Zend\Filter\File\LowerCase',
        'fileRename'                => 'Zend\Filter\File\Rename',
        'fileUppercase'             => 'Zend\Filter\File\UpperCase',
        'htmlEntities'              => 'Zend\Filter\HtmlEntities',
        'inflector'                 => 'Zend\Filter\Inflector',
        'int'                       => 'Zend\Filter\Int',
        'localizedToNormalized'     => 'Zend\Filter\LocalizedToNormalized',
        'normalizedToLocalized'     => 'Zend\Filter\NormalizedToLocalized',
        'null'                      => 'Zend\Filter\Null',
        'numberFormat'              => 'Zend\I18n\Filter\NumberFormat',
        'pregReplace'               => 'Zend\Filter\PregReplace',
        'realPath'                  => 'Zend\Filter\RealPath',
        'stringToLower'             => 'Zend\Filter\StringToLower',
        'stringToUpper'             => 'Zend\Filter\StringToUpper',
        'stringTrim'                => 'Zend\Filter\StringTrim',
        'stripNewLines'             => 'Zend\Filter\StripNewlines',
        'stripTags'                 => 'Zend\Filter\StripTags',
        'wordCamelCaseToDash'       => 'Zend\Filter\Word\CamelCaseToDash',
        'wordCamelCaseToSeparator'  => 'Zend\Filter\Word\CamelCaseToSeparator',
        'wordCamelCaseToUnderscore' => 'Zend\Filter\Word\CamelCaseToUnderscore',
        'wordDashToCamelCase'       => 'Zend\Filter\Word\DashToCamelCase',
        'wordDashToSeparator'       => 'Zend\Filter\Word\DashToSeparator',
        'wordDashToUnderscore'      => 'Zend\Filter\Word\DashToUnderscore',
        'wordSeparatorToCamelCase'  => 'Zend\Filter\Word\SeparatorToCamelCase',
        'wordSeparatorToDash'       => 'Zend\Filter\Word\SeparatorToDash',
        'wordSeparatorToSeparator'  => 'Zend\Filter\Word\SeparatorToSeparator',
        'wordUnderscoreToCamelCase' => 'Zend\Filter\Word\UnderscoreToCamelCase',
        'wordUnderscoreToDash'      => 'Zend\Filter\Word\UnderscoreToDash',
        'wordUnderscoreToSeparator' => 'Zend\Filter\Word\UnderscoreToSeparator',
    );

    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof FilterInterface) {
            // we're okay
            return;
        }
        if (is_callable($plugin)) {
            // also okay
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement %s\FilterInterface or be callable',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
