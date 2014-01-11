<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

use Zend\Framework\Event\EventInterface as Event;
use Zend\View\Renderer\RendererInterface as ViewRenderer;
use Zend\View\Model\ModelInterface as ViewModel;

interface EventInterface
    extends Event
{
    /**
     * @return mixed
     */
    public function result();

    /**
     * @param mixed $result
     * @return self
     */
    public function setResult($result);

    /**
     * @return bool|ViewRenderer
     */
    public function viewRenderer();

    /**
     * @param ViewRenderer $renderer
     * @return self
     */
    public function setViewRenderer(ViewRenderer $renderer);

    /**
     * Target
     *
     * @return ViewModel
     */
    public function target();
}
