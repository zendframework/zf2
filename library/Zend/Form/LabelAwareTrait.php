<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form;

class LabelAwareTrait
{
    /**
     * Label content
     *
     * @var string
     */
    protected $label;

    /**
     * Label specific html attributes
     *
     * @var array
     */
    protected $labelAttributes;

    /**
     * Label specific options
     *
     * @var array
     */
    protected $labelOptions = array(
        'disable_html_escape' => false
    );

    /**
     * Set the label used for this element
     *
     * @param $label
     * @return LabelAwareInterface
     */
    public function setLabel($label)
    {
        if (is_string($label)) {
            $this->label = $label;
        }

        return $this;
    }

    /**
     * Retrieve the label used for this element
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the attributes to use with the label
     *
     * @param array $labelAttributes
     * @return LabelAwareInterface
     */
    public function setLabelAttributes(array $labelAttributes)
    {
        $this->labelAttributes = $labelAttributes;
        return $this;
    }

    /**
     * Get the attributes to use with the label
     *
     * @return array
     */
    public function getLabelAttributes()
    {
        return $this->labelAttributes;
    }

    /**
     * Set label specific options
     *
     * @param array $labelOptions
     * @return LabelAwareInterface
     */
    public function setLabelOptions(array $labelOptions)
    {
        $this->labelOptions = $labelOptions;
        return $this;
    }

    /**
     * Get label specific options
     *
     * @return array
     */
    public function getLabelOptions()
    {
        return $this->labelOptions;
    }
}
