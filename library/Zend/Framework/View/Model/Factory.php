<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Model;

use Zend\Framework\View\ServicesTrait as View;
use Zend\Framework\Service\EventInterface;
use Zend\Framework\Service\Factory\Listener as FactoryListener;

class Factory
    extends FactoryListener
{
    /**
     *
     */
    use View;

    /**
     * @param EventInterface $event
     * @return ViewModel
     */
    public function __invoke(EventInterface $event)
    {
        $vm = new ViewModel;

        $vm->setTemplate($this->viewManager()->layoutTemplate());

        return $vm;
    }
}
