<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace Zend\Validator;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * @category   Zend
 * @package    Zend_Validator
 */
class ValidatorPluginManager extends AbstractPluginManager
{
    /**
     * Default set of validators
     *
     * @var array
     */
    protected $invokableClasses = array(
        'alnum'                    => 'Zend\I18n\Validator\Alnum',
        'alpha'                    => 'Zend\I18n\Validator\Alpha',
        'barcodeCode25interleaved' => 'Zend\Validator\Barcode\Code25interleaved',
        'barcodeCode25'            => 'Zend\Validator\Barcode\Code25',
        'barcodeCode39ext'         => 'Zend\Validator\Barcode\Code39ext',
        'barcodeCode39'            => 'Zend\Validator\Barcode\Code39',
        'barcodeCode93ext'         => 'Zend\Validator\Barcode\Code93ext',
        'barcodeCode93'            => 'Zend\Validator\Barcode\Code93',
        'barcodeEan12'             => 'Zend\Validator\Barcode\Ean12',
        'barcodeEan13'             => 'Zend\Validator\Barcode\Ean13',
        'barcodeEan14'             => 'Zend\Validator\Barcode\Ean14',
        'barcodeEan18'             => 'Zend\Validator\Barcode\Ean18',
        'barcodeEan2'              => 'Zend\Validator\Barcode\Ean2',
        'barcodeEan5'              => 'Zend\Validator\Barcode\Ean5',
        'barcodeEan8'              => 'Zend\Validator\Barcode\Ean8',
        'barcodeGtin12'            => 'Zend\Validator\Barcode\Gtin12',
        'barcodeGtin13'            => 'Zend\Validator\Barcode\Gtin13',
        'barcodeGtin14'            => 'Zend\Validator\Barcode\Gtin14',
        'barcodeIdentcode'         => 'Zend\Validator\Barcode\Identcode',
        'barcodeIntelligentmail'   => 'Zend\Validator\Barcode\Intelligentmail',
        'barcodeIssn'              => 'Zend\Validator\Barcode\Issn',
        'barcodeItf14'             => 'Zend\Validator\Barcode\Itf14',
        'barcodeLeitcode'          => 'Zend\Validator\Barcode\Leitcode',
        'barcodePlanet'            => 'Zend\Validator\Barcode\Planet',
        'barcodePostnet'           => 'Zend\Validator\Barcode\Postnet',
        'barcodeRoyalmail'         => 'Zend\Validator\Barcode\Royalmail',
        'barcodeSscc'              => 'Zend\Validator\Barcode\Sscc',
        'barcodeUpca'              => 'Zend\Validator\Barcode\Upca',
        'barcodeUpce'              => 'Zend\Validator\Barcode\Upce',
        'barcode'                  => 'Zend\Validator\Barcode',
        'between'                  => 'Zend\Validator\Between',
        'callback'                 => 'Zend\Validator\Callback',
        'creditCard'               => 'Zend\Validator\CreditCard',
        'csrf'                     => 'Zend\Validator\Csrf',
        'date'                     => 'Zend\Validator\Date',
        'dateStep'                 => 'Zend\Validator\DateStep',
        'dbNoRecordExists'         => 'Zend\Validator\Db\NoRecordExists',
        'dbRecordExists'           => 'Zend\Validator\Db\RecordExists',
        'digits'                   => 'Zend\Validator\Digits',
        'emailAddress'             => 'Zend\Validator\EmailAddress',
        'explode'                  => 'Zend\Validator\Explode',
        'fileCount'                => 'Zend\Validator\File\Count',
        'fileCrc32'                => 'Zend\Validator\File\Crc32',
        'fileExcludeExtension'     => 'Zend\Validator\File\ExcludeExtension',
        'fileExcludeMimeType'      => 'Zend\Validator\File\ExcludeMimeType',
        'fileExists'               => 'Zend\Validator\File\Exists',
        'fileExtension'            => 'Zend\Validator\File\Extension',
        'fileFilesSize'            => 'Zend\Validator\File\FilesSize',
        'fileHash'                 => 'Zend\Validator\File\Hash',
        'fileImageSize'            => 'Zend\Validator\File\ImageSize',
        'fileIsCompressed'         => 'Zend\Validator\File\IsCompressed',
        'fileIsImage'              => 'Zend\Validator\File\IsImage',
        'fileMd5'                  => 'Zend\Validator\File\Md5',
        'fileMimeType'             => 'Zend\Validator\File\MimeType',
        'fileNotExists'            => 'Zend\Validator\File\NotExists',
        'fileSha1'                 => 'Zend\Validator\File\Sha1',
        'fileSize'                 => 'Zend\Validator\File\Size',
        'fileUpload'               => 'Zend\Validator\File\Upload',
        'fileWordCount'            => 'Zend\Validator\File\WordCount',
        'float'                    => 'Zend\I18n\Validator\Float',
        'greaterThan'              => 'Zend\Validator\GreaterThan',
        'hex'                      => 'Zend\Validator\Hex',
        'hostname'                 => 'Zend\Validator\Hostname',
        'iban'                     => 'Zend\I18n\Validator\Iban',
        'identical'                => 'Zend\Validator\Identical',
        'inArray'                  => 'Zend\Validator\InArray',
        'int'                      => 'Zend\I18n\Validator\Int',
        'ip'                       => 'Zend\Validator\Ip',
        'isbn'                     => 'Zend\Validator\Isbn',
        'lessThan'                 => 'Zend\Validator\LessThan',
        'notEmpty'                 => 'Zend\Validator\NotEmpty',
        'postcode'                 => 'Zend\I18n\Validator\PostCode',
        'regex'                    => 'Zend\Validator\Regex',
        'sitemapChangefreq'        => 'Zend\Validator\Sitemap\Changefreq',
        'sitemapLastmod'           => 'Zend\Validator\Sitemap\Lastmod',
        'sitemapLoc'               => 'Zend\Validator\Sitemap\Loc',
        'sitemapPriority'          => 'Zend\Validator\Sitemap\Priority',
        'stringLength'             => 'Zend\Validator\StringLength',
        'step'                     => 'Zend\Validator\Step',
    );

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
        if ($plugin instanceof ValidatorInterface) {
            // we're okay
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement %s\ValidatorInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
