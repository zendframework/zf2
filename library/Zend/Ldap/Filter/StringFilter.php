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
 * @package    Zend_Ldap
 * @subpackage Filter
 */

namespace Zend\Ldap\Filter;

/**
 * Zend\Ldap\Filter\StringFilter provides a simple custom string filter.
 *
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage Filter
 */
class StringFilter extends AbstractFilter
{
    /**
     * The filter.
     *
     * @var string
     */
    protected $filter;

    /**
     * Creates a Zend\Ldap\Filter\StringFilter.
     *
     * @param string $filter
     */
    public function __construct($filter)
    {
        $this->filter = $filter;
    }

    /**
     * Returns a string representation of the filter.
     *
     * @return string
     */
    public function toString()
    {
        return '(' . $this->filter . ')';
    }
}
