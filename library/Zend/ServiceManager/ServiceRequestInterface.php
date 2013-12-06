<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager;

interface ServiceRequestInterface
{
    public function getName();
    public function setName($name);
    public function isShared();
    public function getTarget();
    public function setTarget($target);
    public function getListeners();
    public function __invoke($listener);
}
