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
 * @package    Zend_Dojo
 * @subpackage View
 */

namespace Zend\Dojo\View\Helper;

/**
 * Dojo Button dijit tied to submit input
 *
 * @package    Zend_Dojo
 * @subpackage View
 */
class SubmitButton extends Button
{
    /**
     * @var string Submit input
     */
    protected $_elementType = 'submit';

    /**
     * dijit.form.Button tied to submit input
     *
     * @param  string $id
     * @param  string $value
     * @param  array $params  Parameters to use for dijit creation
     * @param  array $attribs HTML attributes
     * @return string
     */
    public function __invoke($id = null, $value = null, array $params = array(), array $attribs = array())
    {
        if (!array_key_exists('label', $params)) {
            $params['label'] = $value;
        }
        if (empty($params['label']) && !empty($params['content'])) {
            $params['label'] = $params['content'];
            $value = $params['content'];
        }
        if (empty($params['label']) && !empty($attribs['content'])) {
            $params['label'] = $attribs['content'];
            $value = $attribs['content'];
            unset($attribs['content']);
        }
        return $this->_createFormElement($id, $value, $params, $attribs);
    }
}
