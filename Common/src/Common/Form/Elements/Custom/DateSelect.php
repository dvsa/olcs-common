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
        $minYear = $this->getOption('min_year_delta');

        if (isset($minYear)) {
            $minYear = date('Y', strtotime($minYear . ' years'));
        } else {

            // the minimum year is either:
            // a) the input value's year, if less than the current year
            // b) the current year if it has no value or it's a forthcoming year
            $refStamp = strtotime($this->getValue());
            $currentYear = date('Y');

            if ($refStamp !== false) {
                $minYear = date('Y', $refStamp);
                if ($minYear > $currentYear) {
                    $minYear = $currentYear;
                }
            } else {
                $minYear = $currentYear;
            }
        }

        $this->setMinYear($minYear);

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

    public function setOptions($options)
    {
        parent::setOptions($options);

        if ($this->getOption('max_year_delta')) {
            $maxYear = date('Y', strtotime($this->getOption('max_year_delta') . ' years'));

            $minYear = $this->getOption('min_year_delta');

            if (isset($minYear)) {
                $minYear = date('Y', strtotime($minYear . ' years'));
            } else {
                // if there's no delta specified, initially set the minimum year
                // to the current year
                $minYear = date('Y');
            }

            $this->setMinYear($minYear);
            $this->setMaxYear($maxYear);
        }
    }

}
