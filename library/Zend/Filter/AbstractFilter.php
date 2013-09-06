<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

abstract class AbstractFilter implements FilterInterface
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
            $inflectedKey = str_replace('_', '', $key);

            $setter = 'set' . $inflectedKey;
            if (method_exists($this, $setter)) {
                $this->$setter($value);
                continue;
            }

            $isser = 'is' . $inflectedKey;
            if (method_exists($this, $isser)) {
                $this->$isser($value);
                continue;
            }

            $hasser = 'has' . $inflectedKey;
            if (method_exists($this, $hasser)) {
                $this->$hasser($value);
                continue;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke($value)
    {
        return $this->filter($value);
    }
}
