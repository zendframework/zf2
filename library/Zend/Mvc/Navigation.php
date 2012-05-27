<?php

namespace Zend\Mvc;

use Zend\Mvc\Application;
use Zend\Navigation\AbstractContainer;
use Zend\Navigation\Page\Mvc as MvcPage;

class Navigation extends AbstractContainer
{
    /**
     * @var Application
     */
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function addPage($page)
    {
        $hasMvc = is_array($page) && (isset($page['action']) || isset($page['controller']) || isset($page['route']));
        if ($page instanceof MvcPage || $hasMvc) {
            $locator    = $this->getApplication()->getServiceManager();
            $broker     = $locator->get('ViewHelperBroker');
            $urlHelper  = $broker->load('url');
            $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();

            if (is_array($page)) {
                $page['urlHelper']  = $urlHelper;
                $page['routeMatch'] = $routeMatch;
            } else {
                $page->setUrlHelper($urlHelper);
                $page->setRouteMatch($routeMatch);
            }
        }

        parent::addPage($page);
    }

    /**
     * @param \Zend\Mvc\Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }
}