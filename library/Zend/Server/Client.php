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
 * @package    Zend_Server
 */

namespace Zend\Server;

/**
 * Client Interface
 *
 * @category   Zend
 * @package    Zend_Server
 */
interface Client
{
    /**
     * Executes remote call
     *
     * Unified interface for calling custome remote methods.
     *
     * @param  string $method Remote call name.
     * @param  array $params Call parameters.
     * @return mixed Remote call results.
     */
    public function call($method, $params = array());
}
