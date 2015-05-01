<?php

/**
 * DateSelect
 *
 * @author Someone <someone@valtech.co.uk>
 */
namespace Common\Form\Elements\Custom;

use Zend\Form\Element as ZendElement;

/**
 * DateSelect
 *
 * @author Someone <someone@valtech.co.uk>
 */
class DateSelect extends ZendElement\DateSelect
{
    use Traits\YearDelta;

    public function getInputSpecification()
    {
        return array(
            'name' => $this->getName(),
            'required' => $this->getOption('required'),
            'filters' => array(
                array(
                    'name'    => 'Callback',
                    'options' => array(
                        'callback' => function ($date) {
                            // Convert the date to a specific format
                            if (!is_array($date) || empty($date['year']) ||
                                empty($date['month']) || empty($date['day'])) {
                                return null;
                            }

                            return $date['year'] . '-' . $date['month'] . '-' . $date['day'];
                        }
                    )
                )
            ),
            'validators' => array(
                $this->getValidator(),
            )
        );
    }
}
