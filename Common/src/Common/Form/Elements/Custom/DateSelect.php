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
