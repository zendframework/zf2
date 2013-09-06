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
 * Filter that transforms underscore_separated words to any separator (eg.: my_example to my:example)
 */
class UnderscoreToSeparator extends SeparatorToSeparator
{
    /**
     * @param array|\Traversable $options
     */
    public function __construct(array $options = array())
    {
        $replacementSeparator = isset($options['replacement_separator']) ? $options['replacement_separator'] : ' ';

        parent::__construct(array(
            'search_separator'      => '_',
            'replacement_separator' => $replacementSeparator
        ));
    }
}
