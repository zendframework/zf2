<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Hydrator\Normalizer;

/**
 * This trait adds the ability to have a normalizer
 */
trait ProvidesNormalizerTrait
{
    /**
     * @var NormalizerInterface|null
     */
    protected $normalizer = null;

    /**
     * Set the normalizer
     *
     * @param  NormalizerInterface $normalizer
     * @return void
     */
    public function setNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * Get the normalizer
     *
     * @return NormalizerInterface|null
     */
    public function getNormalizer()
    {
        return $this->normalizer;
    }
}
