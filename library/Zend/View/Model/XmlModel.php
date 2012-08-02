<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace Zend\View\Model;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Model
 */
class XmlModel extends ViewModel
{
    /**
     * Xml probably won't need to be captured into a
     * a parent container by default.
     *
     * @var string
     */
    protected $captureTo = null;

    /**
     * Xml is usually terminal
     *
     * @var bool
     */
    protected $terminate = true;

    /**
     * @var string
     */
    protected $encoding = 'utf-8';

    /**
     * @param string $encoding
     *
     * @return JsonModel
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }
}
