<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter\Word;

/**
 * Filter that transforms any separator to dashed separator (eg.: my-example to myExample)
 */
class SeparatorToDash extends SeparatorToSeparator
{
    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $searchSeparator = isset($options['search_separator']) ? $options['search_separator'] : ' ';

        parent::__construct(array(
            'search_separator'      => $searchSeparator,
            'replacement_separator' => '-'
        ));
    }
}
