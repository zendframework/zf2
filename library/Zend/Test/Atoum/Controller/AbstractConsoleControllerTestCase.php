<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */
namespace Zend\Test\Atoum\Controller;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage Atoum
 */
class AbstractConsoleControllerTestCase extends AbstractControllerTestCase
{
    public function __construct(score $score = null, locale $locale = null, adapter $adapter = null)
    {
        parent::__construct($score, $locale, $adapter);
        $this->getSharedService()->setUseConsoleRequest(true);
    }
}
