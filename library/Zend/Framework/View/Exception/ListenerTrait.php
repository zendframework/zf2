<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Exception;

use Zend\Framework\EventManager\ListenerTrait as ListenerService;
use Zend\Framework\Mvc\Service\ListenerInterface as ServiceManager;

trait ListenerTrait
{
    /**
     *
     */
    use ListenerService;

    /**
     * Display exceptions?
     * @var bool
     */
    protected $displayExceptions = false;

    /**
     * Name of exception template
     * @var string
     */
    protected $exceptionTemplate = 'error';

    /**
     * @param ServiceManager $sm
     * @return self
     */
    public function createService(ServiceManager $sm)
    {
        $vm = $sm->getViewManager();

        $this->setDisplayExceptions($vm->displayExceptions());

        $this->setExceptionTemplate($vm->exceptionTemplate());

        return $this;
    }

    /**
     * Flag: display exceptions in error pages?
     *
     * @param  bool $displayExceptions
     * @return self
     */
    public function setDisplayExceptions($displayExceptions)
    {
        $this->displayExceptions = (bool) $displayExceptions;
        return $this;
    }

    /**
     * Should we display exceptions in error pages?
     *
     * @return bool
     */
    public function displayExceptions()
    {
        return $this->displayExceptions;
    }

    /**
     * Set the exception template
     *
     * @param  string $exceptionTemplate
     * @return self
     */
    public function setExceptionTemplate($exceptionTemplate)
    {
        $this->exceptionTemplate = (string) $exceptionTemplate;
        return $this;
    }

    /**
     * Retrieve the exception template
     *
     * @return string
     */
    public function getExceptionTemplate()
    {
        return $this->exceptionTemplate;
    }
}
