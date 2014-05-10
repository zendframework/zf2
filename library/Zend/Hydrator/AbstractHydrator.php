<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Hydrator;

use Zend\Hydrator\Filter\CompositeFilter;
use Zend\Hydrator\Filter\ProvidesCompositeFilterTrait;
use Zend\Hydrator\Normalizer\ProvidesNormalizerTrait;
use Zend\Hydrator\Strategy\ProvidesStrategiesTrait;

/**
 * This abstract hydrator provides a built-in support for filters and strategies. All
 * standards ZF3 hydrators extend this class
 */
abstract class AbstractHydrator implements HydratorInterface
{
    use ProvidesCompositeFilterTrait;
    use ProvidesNormalizerTrait;
    use ProvidesStrategiesTrait;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->compositeFilter = new CompositeFilter();
    }

    /**
     * Extract the value using a strategy, if one is set
     *
     * @param  string $property The name of the property
     * @param  mixed  $value    The value to extract
     * @param  object $object   The context
     * @return mixed
     */
    public function extractValue($property, $value, $object)
    {
        if ($this->hasStrategy($property)) {
            return $this->getStrategy($property)->extract($value, $object);
        }

        return $value;
    }

    /**
     * Hydrate the value using a strategy, if one is st
     *
     * @param  string $property The name of the property
     * @param  mixed  $value    The value to hydrate
     * @param  array  $data     The context
     * @return mixed
     */
    public function hydrateValue($property, $value, $data)
    {
        if ($this->hasStrategy($property)) {
            return $this->getStrategy($property)->hydrate($value, $data);
        }

        return $value;
    }
}
