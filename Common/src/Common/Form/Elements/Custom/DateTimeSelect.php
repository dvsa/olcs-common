<?php

/**
 * DateTimeSelect
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Form\Elements\Custom;

use Zend\Form\Element as ZendElement;
use Zend\Form\Exception\InvalidArgumentException;

/**
 * DateTimeSelect
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class DateTimeSelect extends ZendElement\DateTimeSelect
{
    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInput()}.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        if ($this->getOption('max_year_delta')) {
            $maxYear = date('Y', strtotime($this->getOption('max_year_delta') . ' years'));

            // the minimum year is either:
            // a) the input value's year, if less than the current year
            // b) the current year if it has no value or it's a forthcoming year
            $refStamp = strtotime($this->getValue());
            $currentYear = date('Y');

            if ($refStamp !== false) {
                $refYear = date('Y', $refStamp);
                if ($refYear > $currentYear) {
                    $refYear = $currentYear;
                }
            } else {
                $refYear = $currentYear;
            }

            $this->setMinYear($refYear);
            $this->setMaxYear($maxYear);
        }

        return array(
            'name' => $this->getName(),
            'required' => $this->getOption('required'),
            'filters' => array(
                array(
                    'name'    => 'Callback',
                    'options' => array(
                        'callback' => function ($date) {
                            // Convert the date to a specific format
                            if (is_array($date)) {
                                if (!isset($date['second'])) {
                                    $date['second'] = '00';
                                }
                                $date = sprintf(
                                    '%s-%s-%s %s:%s:%s',
                                    $date['year'], $date['month'], $date['day'],
                                    $date['hour'], $date['minute'], $date['second']
                                );
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
     * Overrides the default zend behaviour if the value is null
     *
     * @param mixed $value
     * @return void|\Zend\Form\Element
     */
    public function setValue($value)
    {
        if (null === $value) {
            $this->yearElement->setValue(null);
            $this->monthElement->setValue(null);
            $this->dayElement->setValue(null);
            $this->hourElement->setValue(null);
            $this->minuteElement->setValue(null);
            $this->secondElement->setValue('00');
        } else {
            parent::setValue($value);
        }
    }
}
