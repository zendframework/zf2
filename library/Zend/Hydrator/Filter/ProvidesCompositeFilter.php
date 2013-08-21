<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Hydrator\ExtractorFilter;

/**
 * This trait adds the ability to attach extractor filters to a hydrator
 */
trait ProvidesExtractorFilters
{
    /**
     * @var ExtractorFilterPluginManager
     */
    protected $extractorFilterPluginManager;

    /**
     * @var array|ExtractorFilterInterface[]
     */
    protected $compositeFilter;

    /**
     * Set the extractor filter plugin manager
     *
     * @param  ExtractorFilterPluginManager $extractorFilterPluginManager
     * @return void
     */
    public function setExtractorFilterPluginManager(ExtractorFilterPluginManager $extractorFilterPluginManager)
    {
        $this->extractorFilterPluginManager = $extractorFilterPluginManager;
    }

    /**
     * @return ExtractorFilterPluginManager
     */
    public function getExtractorFilterPluginManager()
    {
        if (null === $this->extractorFilterPluginManager) {
            $this->extractorFilterPluginManager = new ExtractorFilterPluginManager();
        }

        return $this->extractorFilterPluginManager;
    }

    /**
     * @return array|ExtractorFilterInterface[]
     */
    public function getCompositeFilter()
    {
        $this->filters[] = new GetExtractorFilter();
        return $this->filters;
    }
}
