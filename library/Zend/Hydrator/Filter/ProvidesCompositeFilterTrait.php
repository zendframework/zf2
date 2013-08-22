<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Hydrator\Filter;

/**
 * This trait adds the ability to attach filters to a hydrator through a composite filter
 */
trait ProvidesCompositeFilterTrait
{
    /**
     * @var CompositeFilter
     */
    protected $compositeFilter;

    /**
     * Get the composite filter
     */
    public function getCompositeFilter()
    {
        return $this->compositeFilter;
    }
}
