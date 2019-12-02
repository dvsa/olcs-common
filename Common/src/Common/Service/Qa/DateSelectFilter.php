<?php

namespace Common\Service\Qa;

use Zend\Filter\DateTimeFormatter;
use Zend\Filter\Exception\InvalidArgumentException;

class DateSelectFilter extends DateTimeFormatter
{
    protected $format = 'Y-m-d';

    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        $valueAsString = implode(
            '-',
            [
                $value['year'],
                $value['month'],
                $value['day']
            ]
        );

        try {
            $result = $this->callParentFilter($valueAsString);
            if (is_null($result)) {
                $result = $valueAsString;
            }
        } catch (InvalidArgumentException $e) {
            $result = $valueAsString;
        }

        return $result;
    }

    /**
     * Call filter from parent class (to facilitate unit testing)
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function callParentFilter($value)
    {
        return parent::filter($value);
    }
}
