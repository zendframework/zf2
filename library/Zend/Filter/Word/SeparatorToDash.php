<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter\Word;

class SeparatorToDash extends SeparatorToSeparator
{
    /**
     * @param array|\Traversable $options
     */
    public function __construct(array $options = array())
    {
        if (!isset($options['search_separator'])) {
            $options['search_separator'] = ' ';
        }

        $options['replacement_operator'] = '-';

        parent::__construct($options);
    }
}
