<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * @param EventInterface $event
     * @param $viewModel
     * @return mixed
     */
    public function trigger(EventInterface $event, $viewModel = null)
    {
        return $this->render($viewModel);
    }
}
