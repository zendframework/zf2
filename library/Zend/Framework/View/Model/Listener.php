<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Model;

use Zend\Framework\Event\EventInterface;
use Zend\View\Model\ClearableModelInterface as ClearableModel;
use Zend\Framework\Event\ListenerTrait as ListenerTrait;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use ListenerTrait,
        ServiceTrait;

    /**
     * @var string
     */
    protected $name = self::EVENT_MODEL;

    /**
     * @param EventInterface $event
     * @param $response
     * @return mixed|void
     */
    public function trigger(EventInterface $event, $response = null)
    {
        if (!$response instanceof ViewModel) {
            return $response;
        }

        if ($response->terminate()) {
            $this->viewModel = $response;
            return $response;
        }

        //if ($event->error() && $this->viewModel instanceof ClearableModel) {
            //$this->viewModel->clearChildren();
        //}

        $this->viewModel->addChild($response);

        return $this->viewModel;
    }
}
