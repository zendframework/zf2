<?php
namespace Zend\Filter;

class StripTabs extends AbstractFilter
{

    /**
     * Strips tabs from $value.
     *
     * @param  string|array $value
     * @return string|array
     */
    public function filter($value)
    {
        if (!is_scalar($value) && !is_array($value)) {
            return $value;
        }

        return str_replace("\t", '', $value);
    }
}
