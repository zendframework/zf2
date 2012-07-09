<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dojo
 */

namespace Zend\Dojo\View\Helper;

/**
 * Dojo VerticalSlider dijit
 *
 * @package    Zend_Dojo
 * @subpackage View
  */
class VerticalSlider extends Slider
{
    /**
     * Dijit being used
     * @var string
     */
    protected $_dijit  = 'dijit.form.VerticalSlider';

    /**
     * Slider type
     * @var string
     */
    protected $_sliderType = 'Vertical';

    /**
     * dijit.form.VerticalSlider
     *
     * @param  int $id
     * @param  mixed $value
     * @param  array $params  Parameters to use for dijit creation
     * @param  array $attribs HTML attributes
     * @return string
     */
    public function __invoke($id = null, $value = null, array $params = array(), array $attribs = array())
    {
        return $this->prepareSlider($id, $value, $params, $attribs);
    }
}
