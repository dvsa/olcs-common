<?php

namespace Common\Service\Qa;

use Zend\Form\Element\DateSelect as ZendDateSelect;
use Zend\Validator\Date as DateValidator;

class DateSelect extends ZendDateSelect
{
    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        // in order to redisplay an invalid date value back to the user, we have to convert it back to an array
        // otherwise, zend will throw an exception when trying to parse the string into a DateTime object
        if (is_string($value)) {
            $valueElements = explode('-', $value);

            $newValue = [
                'year' => $valueElements[0],
                'month' => $valueElements[1],
                'day' => $valueElements[2]
            ];

            $this->callParentSetValue($newValue);

            return;
        }

        $this->callParentSetValue($value);
    }

    /**
     * Call setValue from parent class (to facilitate unit testing)
     *
     * @param mixed $value
     */
    protected function callParentSetValue($value)
    {
        parent::setValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getInputSpecification()
    {
        return [
            'name' => $this->getName(),
            'required' => false,
            'filters' => [
                [
                    'name' => DateSelectFilter::class,
                ]
            ],
            'validators' => [
                [
                    'name' => DateValidator::class,
                    'options' => [
                        'format' => 'Y-m-d',
                        'break_chain_on_failure' => true,
                        'messages' => [
                            DateValidator::INVALID_DATE => 'qanda.date.error.invalid-date'
                        ]
                    ]
                ],
                [
                    'name' => DateNotInPastValidator::class
                ]
            ]
        ];
    }
}
