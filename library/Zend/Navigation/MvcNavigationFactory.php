<?php

namespace Zend\Navigation;

use Zend\Config\Factory as ConfigFactory;
use Zend\Navigation\Exception;
use Zend\Navigation\Navigation;
use Zend\Navigation\Page\Mvc as MvcPage;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MvcNavigationFactory implements FactoryInterface
{
    /**
     * @var array
     */
    protected $pages = array();

    public function __construct($config = null)
    {
        if (null === $config) {
            throw new Exception\InvalidArgumentException(
                'Missing required argument "$config" - expected a filename or' .
                'array of pages.'
            );
        } else if (is_string($config)) {
            if (file_exists($config)) {
                $config = ConfigFactory::fromFile($config);
            } else {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Config was a string but file "%s" does not exist',
                    $config
                ));
            }
        } else if (!is_array($config)) {
            throw new Exception\InvalidArgumentException(
                'Config must be a filename or an array of pages'
            );
        }

        $this->pages = $config;
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $application = $serviceLocator->get('Application');
        $urlHelper   = $serviceLocator->get('ViewHelperBroker')->load('url');
        $routeMatch  = $application->getMvcEvent()->getRouteMatch();

        MvcPage::setDefaultUrlHelper($urlHelper);

        $pages = $this->injectRouteMatch($this->pages, $routeMatch);

        return new Navigation($pages);
    }

    protected function injectRouteMatch($pages, $routeMatch)
    {
        foreach($pages as &$page) {
            $hasMvc = isset($page['action']) || isset($page['controller']) || isset($page['route']);
            if ($hasMvc && !isset($page['routeMatch'])) {
                $page['routeMatch'] = $routeMatch;
            }

            if (isset($page['pages'])) {
                $page['pages'] = $this->injectRouteMatch($page['pages'], $routeMatch);
            }
        }
        return $pages;
    }
}