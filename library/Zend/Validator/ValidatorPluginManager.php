<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Validator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Validator;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ValidatorPluginManager extends AbstractPluginManager
{
    /**
     * Default set of validators
     * 
     * @var array
     */
    protected $invokableClasses = array(
        'alnum'                    => 'Zend\Validator\Alnum',
        'alpha'                    => 'Zend\Validator\Alpha',
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
        'filewordcount'            => 'Zend\Validator\File\WordCount',
        'float'                    => 'Zend\Validator\Float',
        'greaterthan'              => 'Zend\Validator\GreaterThan',
        'hex'                      => 'Zend\Validator\Hex',
        'hostname'                 => 'Zend\Validator\Hostname',
        'iban'                     => 'Zend\Validator\Iban',
        'identical'                => 'Zend\Validator\Identical',
        'inarray'                  => 'Zend\Validator\InArray',
        'int'                      => 'Zend\Validator\Int',
        'ip'                       => 'Zend\Validator\Ip',
        'isbn'                     => 'Zend\Validator\Isbn',
        'lessthan'                 => 'Zend\Validator\LessThan',
        'notempty'                 => 'Zend\Validator\NotEmpty',
        'postcode'                 => 'Zend\Validator\PostCode',
        'regex'                    => 'Zend\Validator\Regex',
        'sitemapchangefreq'        => 'Zend\Validator\Sitemap\Changefreq',
        'sitemaplastmod'           => 'Zend\Validator\Sitemap\Lastmod',
        'sitemaploc'               => 'Zend\Validator\Sitemap\Loc',
        'sitemappriority'          => 'Zend\Validator\Sitemap\Priority',
        'stringlength'             => 'Zend\Validator\StringLength',
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
