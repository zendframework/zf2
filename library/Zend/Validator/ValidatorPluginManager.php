<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;

class ValidatorPluginManager extends AbstractPluginManager
{
    /**
     * Default set of validators
     *
     * @var array
     */
    protected $invokableClasses = array(
        'Zend\I18n\Validator\Alnum'                => 'Zend\I18n\Validator\Alnum',
        'Zend\I18n\Validator\Alpha'                => 'Zend\I18n\Validator\Alpha',
        'Zend\Validator\Barcode\Code25interleaved' => 'Zend\Validator\Barcode\Code25interleaved',
        'Zend\Validator\Barcode\Code25'            => 'Zend\Validator\Barcode\Code25',
        'Zend\Validator\Barcode\Code39ext'         => 'Zend\Validator\Barcode\Code39ext',
        'Zend\Validator\Barcode\Code39'            => 'Zend\Validator\Barcode\Code39',
        'Zend\Validator\Barcode\Code93ext'         => 'Zend\Validator\Barcode\Code93ext',
        'Zend\Validator\Barcode\Code93'            => 'Zend\Validator\Barcode\Code93',
        'Zend\Validator\Barcode\Ean12'             => 'Zend\Validator\Barcode\Ean12',
        'Zend\Validator\Barcode\Ean13'             => 'Zend\Validator\Barcode\Ean13',
        'Zend\Validator\Barcode\Ean14'             => 'Zend\Validator\Barcode\Ean14',
        'Zend\Validator\Barcode\Ean18'             => 'Zend\Validator\Barcode\Ean18',
        'Zend\Validator\Barcode\Ean2'              => 'Zend\Validator\Barcode\Ean2',
        'Zend\Validator\Barcode\Ean5'              => 'Zend\Validator\Barcode\Ean5',
        'Zend\Validator\Barcode\Ean8'              => 'Zend\Validator\Barcode\Ean8',
        'Zend\Validator\Barcode\Gtin12'            => 'Zend\Validator\Barcode\Gtin12',
        'Zend\Validator\Barcode\Gtin13'            => 'Zend\Validator\Barcode\Gtin13',
        'Zend\Validator\Barcode\Gtin14'            => 'Zend\Validator\Barcode\Gtin14',
        'Zend\Validator\Barcode\Identcode'         => 'Zend\Validator\Barcode\Identcode',
        'Zend\Validator\Barcode\Intelligentmail'   => 'Zend\Validator\Barcode\Intelligentmail',
        'Zend\Validator\Barcode\Issn'              => 'Zend\Validator\Barcode\Issn',
        'Zend\Validator\Barcode\Itf14'             => 'Zend\Validator\Barcode\Itf14',
        'Zend\Validator\Barcode\Leitcode'          => 'Zend\Validator\Barcode\Leitcode',
        'Zend\Validator\Barcode\Planet'            => 'Zend\Validator\Barcode\Planet',
        'Zend\Validator\Barcode\Postnet'           => 'Zend\Validator\Barcode\Postnet',
        'Zend\Validator\Barcode\Royalmail'         => 'Zend\Validator\Barcode\Royalmail',
        'Zend\Validator\Barcode\Sscc'              => 'Zend\Validator\Barcode\Sscc',
        'Zend\Validator\Barcode\Upca'              => 'Zend\Validator\Barcode\Upca',
        'Zend\Validator\Barcode\Upce'              => 'Zend\Validator\Barcode\Upce',
        'Zend\Validator\Barcode'                   => 'Zend\Validator\Barcode',
        'Zend\Validator\Between'                   => 'Zend\Validator\Between',
        'Zend\Validator\Callback'                  => 'Zend\Validator\Callback',
        'Zend\Validator\CreditCard'                => 'Zend\Validator\CreditCard',
        'Zend\Validator\Csrf'                      => 'Zend\Validator\Csrf',
        'Zend\Validator\Date'                      => 'Zend\Validator\Date',
        'Zend\Validator\DateStep'                  => 'Zend\Validator\DateStep',
        'Zend\I18n\Validator\DateTime'             => 'Zend\I18n\Validator\DateTime',
        'Zend\Validator\Db\NoRecordExists'         => 'Zend\Validator\Db\NoRecordExists',
        'Zend\Validator\Db\RecordExists'           => 'Zend\Validator\Db\RecordExists',
        'Zend\Validator\Digits'                    => 'Zend\Validator\Digits',
        'Zend\Validator\EmailAddress'              => 'Zend\Validator\EmailAddress',
        'Zend\Validator\Explode'                   => 'Zend\Validator\Explode',
        'Zend\Validator\File\Count'                => 'Zend\Validator\File\Count',
        'Zend\Validator\File\Crc32'                => 'Zend\Validator\File\Crc32',
        'Zend\Validator\File\ExcludeExtension'     => 'Zend\Validator\File\ExcludeExtension',
        'Zend\Validator\File\ExcludeMimeType'      => 'Zend\Validator\File\ExcludeMimeType',
        'Zend\Validator\File\Exists'               => 'Zend\Validator\File\Exists',
        'Zend\Validator\File\Extension'            => 'Zend\Validator\File\Extension',
        'Zend\Validator\File\FilesSize'            => 'Zend\Validator\File\FilesSize',
        'Zend\Validator\File\Hash'                 => 'Zend\Validator\File\Hash',
        'Zend\Validator\File\ImageSize'            => 'Zend\Validator\File\ImageSize',
        'Zend\Validator\File\IsCompressed'         => 'Zend\Validator\File\IsCompressed',
        'Zend\Validator\File\IsImage'              => 'Zend\Validator\File\IsImage',
        'Zend\Validator\File\Md5'                  => 'Zend\Validator\File\Md5',
        'Zend\Validator\File\MimeType'             => 'Zend\Validator\File\MimeType',
        'Zend\Validator\File\NotExists'            => 'Zend\Validator\File\NotExists',
        'Zend\Validator\File\Sha1'                 => 'Zend\Validator\File\Sha1',
        'Zend\Validator\File\Size'                 => 'Zend\Validator\File\Size',
        'Zend\Validator\File\Upload'               => 'Zend\Validator\File\Upload',
        'Zend\Validator\File\UploadFile'           => 'Zend\Validator\File\UploadFile',
        'Zend\Validator\File\WordCount'            => 'Zend\Validator\File\WordCount',
        'Zend\I18n\Validator\Float'                => 'Zend\I18n\Validator\Float',
        'Zend\Validator\GreaterThan'               => 'Zend\Validator\GreaterThan',
        'Zend\Validator\Hex'                       => 'Zend\Validator\Hex',
        'Zend\Validator\Hostname'                  => 'Zend\Validator\Hostname',
        'Zend\Validator\Iban'                      => 'Zend\Validator\Iban',
        'Zend\Validator\Identical'                 => 'Zend\Validator\Identical',
        'Zend\Validator\InArray'                   => 'Zend\Validator\InArray',
        'Zend\I18n\Validator\Int'                  => 'Zend\I18n\Validator\Int',
        'Zend\Validator\Ip'                        => 'Zend\Validator\Ip',
        'Zend\Validator\Isbn'                      => 'Zend\Validator\Isbn',
        'Zend\Validator\IsInstanceOf'              => 'Zend\Validator\IsInstanceOf',
        'Zend\Validator\LessThan'                  => 'Zend\Validator\LessThan',
        'Zend\Validator\NotEmpty'                  => 'Zend\Validator\NotEmpty',
        'Zend\I18n\Validator\PhoneNumber'          => 'Zend\I18n\Validator\PhoneNumber',
        'Zend\I18n\Validator\PostCode'             => 'Zend\I18n\Validator\PostCode',
        'Zend\Validator\Regex'                     => 'Zend\Validator\Regex',
        'Zend\Validator\Sitemap\Changefreq'        => 'Zend\Validator\Sitemap\Changefreq',
        'Zend\Validator\Sitemap\Lastmod'           => 'Zend\Validator\Sitemap\Lastmod',
        'Zend\Validator\Sitemap\Loc'               => 'Zend\Validator\Sitemap\Loc',
        'Zend\Validator\Sitemap\Priority'          => 'Zend\Validator\Sitemap\Priority',
        'Zend\Validator\StringLength'              => 'Zend\Validator\StringLength',
        'Zend\Validator\Step'                      => 'Zend\Validator\Step',
        'Zend\Validator\Uri'                       => 'Zend\Validator\Uri',
    );

    /**
     * @var array
     */
    protected $factories = array(
        'Zend\Validator\ValidatorChain' => 'Zend\Validator\Factory\ValidatorChainFactory'
    );

    /**
     * List of aliases
     *
     * @var array
     */
    protected $aliases = array(
        'alnum'                    => 'Zend\I18n\Validator\Alnum',
        'alpha'                    => 'Zend\I18n\Validator\Alpha',
        'barcodecode25interleaved' => 'Zend\Validator\Barcode\Code25interleaved',
        'barcodecode25'            => 'Zend\Validator\Barcode\Code25',
        'barcodecode39ext'         => 'Zend\Validator\Barcode\Code39ext',
        'barcodecode39'            => 'Zend\Validator\Barcode\Code39',
        'barcodecode93ext'         => 'Zend\Validator\Barcode\Code93ext',
        'barcodecode93'            => 'Zend\Validator\Barcode\Code93',
        'barcodeean12'             => 'Zend\Validator\Barcode\Ean12',
        'barcodeean13'             => 'Zend\Validator\Barcode\Ean13',
        'barcodeean14'             => 'Zend\Validator\Barcode\Ean14',
        'barcodeean18'             => 'Zend\Validator\Barcode\Ean18',
        'barcodeean2'              => 'Zend\Validator\Barcode\Ean2',
        'barcodeean5'              => 'Zend\Validator\Barcode\Ean5',
        'barcodeean8'              => 'Zend\Validator\Barcode\Ean8',
        'barcodegtin12'            => 'Zend\Validator\Barcode\Gtin12',
        'barcodegtin13'            => 'Zend\Validator\Barcode\Gtin13',
        'barcodegtin14'            => 'Zend\Validator\Barcode\Gtin14',
        'barcodeidentcode'         => 'Zend\Validator\Barcode\Identcode',
        'barcodeintelligentmail'   => 'Zend\Validator\Barcode\Intelligentmail',
        'barcodeissn'              => 'Zend\Validator\Barcode\Issn',
        'barcodeitf14'             => 'Zend\Validator\Barcode\Itf14',
        'barcodeleitcode'          => 'Zend\Validator\Barcode\Leitcode',
        'barcodeplanet'            => 'Zend\Validator\Barcode\Planet',
        'barcodepostnet'           => 'Zend\Validator\Barcode\Postnet',
        'barcoderoyalmail'         => 'Zend\Validator\Barcode\Royalmail',
        'barcodesscc'              => 'Zend\Validator\Barcode\Sscc',
        'barcodeupca'              => 'Zend\Validator\Barcode\Upca',
        'barcodeupce'              => 'Zend\Validator\Barcode\Upce',
        'barcode'                  => 'Zend\Validator\Barcode',
        'between'                  => 'Zend\Validator\Between',
        'callback'                 => 'Zend\Validator\Callback',
        'creditcard'               => 'Zend\Validator\CreditCard',
        'csrf'                     => 'Zend\Validator\Csrf',
        'date'                     => 'Zend\Validator\Date',
        'datestep'                 => 'Zend\Validator\DateStep',
        'datetime'                 => 'Zend\I18n\Validator\DateTime',
        'dbnorecordexists'         => 'Zend\Validator\Db\NoRecordExists',
        'dbrecordexists'           => 'Zend\Validator\Db\RecordExists',
        'digits'                   => 'Zend\Validator\Digits',
        'emailaddress'             => 'Zend\Validator\EmailAddress',
        'explode'                  => 'Zend\Validator\Explode',
        'filecount'                => 'Zend\Validator\File\Count',
        'filecrc32'                => 'Zend\Validator\File\Crc32',
        'fileexcludeextension'     => 'Zend\Validator\File\ExcludeExtension',
        'fileexcludemimetype'      => 'Zend\Validator\File\ExcludeMimeType',
        'fileexists'               => 'Zend\Validator\File\Exists',
        'fileextension'            => 'Zend\Validator\File\Extension',
        'filefilessize'            => 'Zend\Validator\File\FilesSize',
        'filehash'                 => 'Zend\Validator\File\Hash',
        'fileimagesize'            => 'Zend\Validator\File\ImageSize',
        'fileiscompressed'         => 'Zend\Validator\File\IsCompressed',
        'fileisimage'              => 'Zend\Validator\File\IsImage',
        'filemd5'                  => 'Zend\Validator\File\Md5',
        'filemimetype'             => 'Zend\Validator\File\MimeType',
        'filenotexists'            => 'Zend\Validator\File\NotExists',
        'filesha1'                 => 'Zend\Validator\File\Sha1',
        'filesize'                 => 'Zend\Validator\File\Size',
        'fileupload'               => 'Zend\Validator\File\Upload',
        'fileuploadfile'           => 'Zend\Validator\File\UploadFile',
        'filewordcount'            => 'Zend\Validator\File\WordCount',
        'float'                    => 'Zend\I18n\Validator\Float',
        'greaterthan'              => 'Zend\Validator\GreaterThan',
        'hex'                      => 'Zend\Validator\Hex',
        'hostname'                 => 'Zend\Validator\Hostname',
        'iban'                     => 'Zend\Validator\Iban',
        'identical'                => 'Zend\Validator\Identical',
        'inarray'                  => 'Zend\Validator\InArray',
        'int'                      => 'Zend\I18n\Validator\Int',
        'ip'                       => 'Zend\Validator\Ip',
        'isbn'                     => 'Zend\Validator\Isbn',
        'isinstanceof'             => 'Zend\Validator\IsInstanceOf',
        'lessthan'                 => 'Zend\Validator\LessThan',
        'notempty'                 => 'Zend\Validator\NotEmpty',
        'phonenumber'              => 'Zend\I18n\Validator\PhoneNumber',
        'postcode'                 => 'Zend\I18n\Validator\PostCode',
        'regex'                    => 'Zend\Validator\Regex',
        'sitemapchangefreq'        => 'Zend\Validator\Sitemap\Changefreq',
        'sitemaplastmod'           => 'Zend\Validator\Sitemap\Lastmod',
        'sitemaploc'               => 'Zend\Validator\Sitemap\Loc',
        'sitemappriority'          => 'Zend\Validator\Sitemap\Priority',
        'stringlength'             => 'Zend\Validator\StringLength',
        'step'                     => 'Zend\Validator\Step',
        'uri'                      => 'Zend\Validator\Uri',
    );

    /**
     * Whether or not to share by default; default to false
     *
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * Validate the plugin
     *
     * Checks that the validator loaded is an instance of ValidatorInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof ValidatorInterface || is_callable($plugin)) {
            // we're okay
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement %s\ValidatorInterface or be callable',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
