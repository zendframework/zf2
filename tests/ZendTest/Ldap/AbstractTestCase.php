<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Ldap;

use Zend\Ldap\Node;

/**
 * @group      Zend_Ldap
 */
abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    protected function createTestArrayData()
    {
        $data = [
            'dn'          => 'cn=name,dc=example,dc=org',
            'cn'          => ['name'],
            'host'        => ['a', 'b', 'c'],
            'empty'       => [],
            'boolean'     => ['TRUE', 'FALSE'],
            'objectclass' => ['account', 'top'],
        ];
        return $data;
    }

    /**
     * @return Node
     */
    protected function createTestNode()
    {
        return Node::fromArray($this->createTestArrayData(), true);
    }
}
