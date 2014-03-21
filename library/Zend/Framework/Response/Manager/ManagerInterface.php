<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response\Manager;

use Zend\Stdlib\ResponseInterface;

interface ManagerInterface
{
    /**
     * @param ResponseInterface $response
     * @return mixed
     */
    public function response(ResponseInterface $response);

    /**
     * @param ResponseInterface $response
     * @return mixed
     */
    public function send(ResponseInterface $response);
}
