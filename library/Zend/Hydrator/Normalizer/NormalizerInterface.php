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
 * A normalizer is an object that allow to alter the data before/after hydration/extraction
 *
 * A typical use case of this may occur when using a JavaScript MVC framework. It may send
 * data to your server using some conventions (that you may or may not be able to change),
 * like properties being underscore_separated or wrapped around a root key. On the other
 * hand, you may need to extract data from an object, and send this data in a very specific
 * format that is understandable by the client.
 *
 * When hydrating, your data will first be normalized (through the "normalize" method), so that
 * it can be properly converted to PHP conventions. On the other hand, before after
 * extracting, your data will be denormalized (through the "denormalize" method) to convert it
 * to some other format.
 *
 * Zend Framework 3 does not provide any built-in normalizer, as this is very application
 * specific, but each built-in hydrators that extend AbstractHydrator have the capability to
 * accept a normalizer through the "setNormalizer" method.
 */
interface NormalizerInterface
{
    /**
     * Normalize a set of data (prior to hydration phase)
     *
     * @param  array  $data
     * @param  object $object
     * @return array
     */
    public function normalize(array $data, $object);

    /**
     * Denormalize a set of data (after the extraction phase)
     *
     * @param  array  $data
     * @param  object $object
     * @return array
     */
    public function denormalize(array $data, $object);
}
