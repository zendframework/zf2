<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Code
 */

namespace Zend\Code\Generator\DocBlock\Tag;

use Zend\Code\Generator\DocBlock\Tag;
use Zend\Code\Reflection\DocBlock\Tag\TagInterface as ReflectionDocBlockTag;

/**
 * @category   Zend
 * @package    Zend_Code_Generator
 */
class LicenseTag extends Tag
{
    /**
     * @var string
     */
    protected $url = null;

    /**
     * @var string
     */
    protected $licenseName = null;

    /**
     * @param  ReflectionDocBlockTag $reflectionTagLicense
     * @return LicenseTag
     */
    public static function fromReflection(ReflectionDocBlockTag $reflectionTagLicense)
    {
        $licenseTag = new self();
        $licenseTag
            ->setName('license')
            ->setUrl($reflectionTagLicense->getUrl())
            ->setLicenseName($reflectionTagLicense->getDescription());

        return $licenseTag;
    }

    /**
     * @param  string     $url
     * @return LicenseTag
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param  string     $name
     * @return LicenseTag
     */
    public function setLicenseName($name)
    {
        $this->licenseName = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getLicenseName()
    {
        return $this->licenseName;
    }

    /**
     * @return string
     */
    public function generate()
    {
        $output = '@license '
            . (($this->url != null) ? $this->url : 'unknown')
            . (($this->licenseName != null) ? ' ' . $this->licenseName : '');

        return $output;
    }

}
