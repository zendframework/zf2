<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager implementation for the filters
 *
 * Enforces that filters retrieved are either callbacks or instances of
 * FilterInterface. Additionally, it registers a number of default filters
 * available, as well as aliases for them.
 */
class FilterPluginManager extends AbstractPluginManager
{
    /**
     * Default set of filters
     *
     * @var array
     */
    protected $invokableClasses = array(
        'Zend\Filter\BaseName'                   => 'Zend\Filter\BaseName',
        'Zend\Filter\Callback'                   => 'Zend\Filter\Callback',
        'Zend\Filter\DateTimeFormatter'          => 'Zend\Filter\DateTimeFormatter',
        'Zend\Filter\Digits'                     => 'Zend\Filter\Digits',
        'Zend\Filter\Dir'                        => 'Zend\Filter\Dir',
        'Zend\Filter\File\LowerCase'             => 'Zend\Filter\File\LowerCase',
        'Zend\Filter\File\RenameUpload'          => 'Zend\Filter\File\RenameUpload',
        'Zend\Filter\File\UpperCase'             => 'Zend\Filter\File\UpperCase',
        'Zend\Filter\HtmlEntities'               => 'Zend\Filter\HtmlEntities',
        'Zend\Filter\Inflector'                  => 'Zend\Filter\Inflector',
        'Zend\Filter\Int'                        => 'Zend\Filter\Int',
        'Zend\Filter\Null'                       => 'Zend\Filter\Null',
        'Zend\Filter\PregReplace'                => 'Zend\Filter\PregReplace',
        'Zend\Filter\RealPath'                   => 'Zend\Filter\RealPath',
        'Zend\Filter\StringToLower'              => 'Zend\Filter\StringToLower',
        'Zend\Filter\StringToUpper'              => 'Zend\Filter\StringToUpper',
        'Zend\Filter\StringTrim'                 => 'Zend\Filter\StringTrim',
        'Zend\Filter\StripNewlines'              => 'Zend\Filter\StripNewlines',
        'Zend\Filter\StripTags'                  => 'Zend\Filter\StripTags',
        'Zend\Filter\UriNormalize'               => 'Zend\Filter\UriNormalize',
        'Zend\Filter\Word\CamelCaseToDash'       => 'Zend\Filter\Word\CamelCaseToDash',
        'Zend\Filter\Word\CamelCaseToSeparator'  => 'Zend\Filter\Word\CamelCaseToSeparator',
        'Zend\Filter\Word\CamelCaseToUnderscore' => 'Zend\Filter\Word\CamelCaseToUnderscore',
        'Zend\Filter\Word\DashToCamelCase'       => 'Zend\Filter\Word\DashToCamelCase',
        'Zend\Filter\Word\DashToSeparator'       => 'Zend\Filter\Word\DashToSeparator',
        'Zend\Filter\Word\DashToUnderscore'      => 'Zend\Filter\Word\DashToUnderscore',
        'Zend\Filter\Word\SeparatorToCamelCase'  => 'Zend\Filter\Word\SeparatorToCamelCase',
        'Zend\Filter\Word\SeparatorToDash'       => 'Zend\Filter\Word\SeparatorToDash',
        'Zend\Filter\Word\SeparatorToSeparator'  => 'Zend\Filter\Word\SeparatorToSeparator',
        'Zend\Filter\Word\UnderscoreToCamelCase' => 'Zend\Filter\Word\UnderscoreToCamelCase',
        'Zend\Filter\Word\UnderscoreToDash'      => 'Zend\Filter\Word\UnderscoreToDash',
        'Zend\Filter\Word\UnderscoreToSeparator' => 'Zend\Filter\Word\UnderscoreToSeparator',
        'Zend\I18n\Filter\Alnum'                 => 'Zend\I18n\Filter\Alnum',
        'Zend\I18n\Filter\Alpha'                 => 'Zend\I18n\Filter\Alpha',
        'Zend\I18n\Filter\Boolean'               => 'Zend\I18n\Filter\Boolean',
        'Zend\I18n\Filter\NumberFormat'          => 'Zend\I18n\Filter\NumberFormat',
    );

    /**
     * @var array
     */
    protected $factories = array(
        'Zend\Crypt\Filter\Decrypt'      => 'Zend\Crypt\Factory\DecryptFilterFactory',
        'Zend\Crypt\Filter\Encrypt'      => 'Zend\Crypt\Factory\EncryptFilterFactory',
        'Zend\Crypt\Filter\File\Decrypt' => 'Zend\Crypt\Factory\DecryptFileFilterFactory',
        'Zend\Crypt\Filter\File\Encrypt' => 'Zend\Crypt\Factory\EncryptFileFilterFactory',
        'Zend\Filter\Compress'           => 'Zend\Filter\Factory\CompressFilterFactory',
        'Zend\Filter\Decompress'         => 'Zend\Filter\Factory\DecompressFilterFactory',
        'Zend\Filter\FilterChain'        => 'Zend\Filter\Factory\FilterChainFactory'
    );

    protected $aliases = array(
        'alnum'                     => 'Zend\I18n\Filter\Alnum',
        'alpha'                     => 'Zend\I18n\Filter\Alpha',
        'basename'                  => 'Zend\Filter\BaseName',
        'boolean'                   => 'Zend\I18n\Filter\Boolean',
        'callback'                  => 'Zend\Filter\Callback',
        'compress'                  => 'Zend\Filter\Factory\CompressFilterFactory',
        'datetimeformatter'         => 'Zend\Filter\DateTimeFormatter',
        'decompress'                => 'Zend\Filter\Factory\DecompressFilterFactory',
        'decrypt'                   => 'Zend\Crypt\Filter\Decrypt',
        'digits'                    => 'Zend\Filter\Digits',
        'dir'                       => 'Zend\Filter\Dir',
        'encrypt'                   => 'Zend\Crypt\Filter\Encrypt',
        'filedecrypt'               => 'Zend\Crypt\Filter\File\Decrypt',
        'fileencrypt'               => 'Zend\Crypt\Filter\File\Encrypt',
        'filelowercase'             => 'Zend\Filter\File\LowerCase',
        'filerename'                => 'Zend\Filter\File\Rename',
        'filerenameupload'          => 'Zend\Filter\File\RenameUpload',
        'fileuppercase'             => 'Zend\Filter\File\UpperCase',
        'filterchain'               => 'Zend\Filter\Factory\FilterChainFactory',
        'htmlentities'              => 'Zend\Filter\HtmlEntities',
        'inflector'                 => 'Zend\Filter\Inflector',
        'int'                       => 'Zend\Filter\Int',
        'null'                      => 'Zend\Filter\Null',
        'numberformat'              => 'Zend\I18n\Filter\NumberFormat',
        'pregreplace'               => 'Zend\Filter\PregReplace',
        'realpath'                  => 'Zend\Filter\RealPath',
        'stringtolower'             => 'Zend\Filter\StringToLower',
        'stringtoupper'             => 'Zend\Filter\StringToUpper',
        'stringtrim'                => 'Zend\Filter\StringTrim',
        'stripnewlines'             => 'Zend\Filter\StripNewlines',
        'striptags'                 => 'Zend\Filter\StripTags',
        'urinormalize'              => 'Zend\Filter\UriNormalize',
        'wordcamelcasetodash'       => 'Zend\Filter\Word\CamelCaseToDash',
        'wordcamelcasetoseparator'  => 'Zend\Filter\Word\CamelCaseToSeparator',
        'wordcamelcasetounderscore' => 'Zend\Filter\Word\CamelCaseToUnderscore',
        'worddashtocamelcase'       => 'Zend\Filter\Word\DashToCamelCase',
        'worddashtoseparator'       => 'Zend\Filter\Word\DashToSeparator',
        'worddashtounderscore'      => 'Zend\Filter\Word\DashToUnderscore',
        'wordseparatortocamelcase'  => 'Zend\Filter\Word\SeparatorToCamelCase',
        'wordseparatortodash'       => 'Zend\Filter\Word\SeparatorToDash',
        'wordseparatortoseparator'  => 'Zend\Filter\Word\SeparatorToSeparator',
        'wordunderscoretocamelcase' => 'Zend\Filter\Word\UnderscoreToCamelCase',
        'wordunderscoretodash'      => 'Zend\Filter\Word\UnderscoreToDash',
        'wordunderscoretoseparator' => 'Zend\Filter\Word\UnderscoreToSeparator',
    );

    /**
     * @TODO: Any filter that does not have any option should be shared by default
     *
     * Whether or not to share by default; default to false
     *
     * @var bool
     */
    protected $shareByDefault = false;

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
        if ($plugin instanceof FilterInterface || is_callable($plugin)) {
            // we're okay
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement %s\FilterInterface or be callable',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
