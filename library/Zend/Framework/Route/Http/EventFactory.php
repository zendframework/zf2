<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Http;

use Zend\Framework\Service\Factory\Factory;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\View\Manager\ServicesTrait as ViewManager;

class EventFactory
    extends Factory
{
    /**
     *
     */
    use ViewManager;

    /**
     * @param Request $request
     * @param array $options
     * @return Event
     */
    public function __invoke(Request $request, array $options = [])
    {
        list($httpRequest, $pathOffset) = $options;

        $baseUrl = $httpRequest->getBaseUrl();
        $uri     = $httpRequest->getUri();

        $baseUrlLength = strlen($baseUrl) ? : null;

        if ($pathOffset !== null) {
            $baseUrlLength += $pathOffset;
        }

        $pathLength = $baseUrlLength ? strlen($uri->getPath()) - $baseUrlLength : null;

        return new Event($uri, $pathLength, $baseUrlLength);
    }
}
