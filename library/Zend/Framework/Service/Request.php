<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

class Request
    implements RequestInterface
{
    /**
     *
     */
    use RequestTrait;

    /**
     * @param $service
     * @param $alias
     * @param array $options
     */
    public function __construct($service, $alias, array $options = [])
    {
        $this->alias   = $alias;
        $this->options = $options;
        $this->service = $service;
    }
}
