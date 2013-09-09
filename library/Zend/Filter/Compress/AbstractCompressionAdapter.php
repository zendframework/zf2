<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter\Compress;

use Zend\Stdlib\AbstractOptions;

/**
 * Abstract compression adapter
 */
abstract class AbstractCompressionAdapter implements CompressionAdapterInterface
{
    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    /**
     * @TODO: replace with a generic trait that can be reused
     *
     * Set options for the given filter
     *
     * This method inflect the key names and call the corresponding
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $setter = 'set' . str_replace('_', '', $key);

            if (method_exists($this, $setter)) {
                $this->$setter($value);
                continue;
            }
        }
    }
}
