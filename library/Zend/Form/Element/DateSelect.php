<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace Zend\Form\Element;

use DateTime;
use Zend\Form\Form;
use Zend\Validator\ValidatorInterface;
use Zend\Validator\Date as DateValidator;

class DateSelect extends MonthSelect
{
    /**
     * Select form element that contains values for day
     *
     * @var Select
     */
    protected $dayElement;

    /**
     * Constructor. Add the day select element
     *
     * @param  null|int|string  $name    Optional name for the element
     * @param  array            $options Optional options for the element
     */
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->dayElement = new Select('day');
    }

    /**
     * Accepted options for DateSelect (plus the ones from MonthSelect) :
     * - day_attributes: HTML attributes to be rendered with the day element
     *
     * @param array|\Traversable $options
     * @return DateSelect
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($options['day_attributes'])) {
            $this->setDayAttributes($options['day_attributes']);
        }

        return $this;
    }

    /**
     * @return Select
     */
    public function getDayElement()
    {
        return $this->dayElement;
    }

    /**
     * Set the day attributes
     *
     * @param  array $dayAttributes
     * @return DateSelect
     */
    public function setDayAttributes(array $dayAttributes)
    {
        $this->dayElement->setAttributes($dayAttributes);
        return $this;
    }

    /**
     * Get the day attributes
     *
     * @return array
     */
    public function getDayAttributes()
    {
        return $this->dayElement->getAttributes();
    }

    /**
     * @param mixed $value
     * @return void|\Zend\Form\Element
     */
    public function setValue($value)
    {
        if ($value instanceof DateTime) {
            $value = array(
                'year'  => $value->format('Y'),
                'month' => $value->format('m'),
                'day'   => $value->format('d')
            );
        }

        $this->yearElement->setValue($value['year']);
        $this->monthElement->setValue($value['month']);
        $this->dayElement->setValue($value['day']);
    }

    /**
     * Prepare the form element (mostly used for rendering purposes)
     *
     * @param Form $form
     * @return mixed
     */
    public function prepareElement(Form $form)
    {
        parent::prepareElement($form);

        $name = $this->getName();
        $this->dayElement->setName($name . '[day]');
    }

    /**
     * Get validator
     *
     * @return ValidatorInterface
     */
    protected function getValidator()
    {
        if (null === $this->validator) {
            $this->validator = new DateValidator(array('format' => 'Y-m-d'));
        }

        return $this->validator;
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInput()}.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        return array(
            'name' => $this->getName(),
            'required' => false,
            'filters' => array(
                array(
                    'name'    => 'Callback',
                    'options' => array(
                        'callback' => function($date) {
                            // Convert the date to a specific format
                            if (is_array($date)) {
                                $date = $date['year'] . '-' . $date['month'] . '-' . $date['day'];
                            }

                            return $date;
                        }
                    )
                )
            ),
            'validators' => array(
                $this->getValidator(),
            )
        );
    }

    /**
     * Clone the element (this is needed by Collection element, as it needs different copies of the elements)
     */
    public function __clone()
    {
        $this->dayElement   = clone $this->dayElement;
        $this->monthElement = clone $this->monthElement;
        $this->yearElement  = clone $this->yearElement;
    }
}

