<?php

namespace Common\Validator;

use Common\Filter\DateSelectNullifier;
use Common\Filter\DateTimeSelectNullifier;

/**
 * Class DateCompare - used to validate two dates via a legal operator:
 * 'gt' -> greater than
 * 'gte' -> greater than or equal to
 * 'lt' -> less than
 * 'lte' -> less than or equal to
 * @package Common\Validator
 */
class DateCompare extends AbstractCompare
{
    const DATE_FORMAT = 'Y-m-d';
    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * Error messages
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_GTE => "This date must be after or the same as '%compare_to_label%'",
        self::NOT_GT => "This date must be after '%compare_to_label%'",
        self::NOT_LTE => "This date must be before or the same as '%compare_to_label%'",
        self::NOT_LT => "This date must be before '%compare_to_label%'",
        self::INVALID_OPERATOR => "Invalid operator",
        self::INVALID_FIELD => "Input field being compared to doesn't exist",
        self::NO_COMPARE => "Unable to compare with '%compare_to_label%'"
    );

    /**
     * Whether we're comparing the time also
     * @var bool
     */
    protected $hasTime = false;

    /**
     * @param bool $hasTime
     * @return $this
     */
    public function setHasTime($hasTime)
    {
        $this->hasTime = (boolean) $hasTime;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasTime()
    {
        return $this->hasTime;
    }

    /**
     * Sets options
     *
     * @param  array $options
     * @return DateCompare
     */
    public function setOptions($options = array())
    {
        if (isset($options['has_time'])) {
            $this->setHasTime($options['has_time']);
        }

        return parent::setOptions($options);
    }

    /**
     * Returns true if and only if a token has been set and the provided value
     * matches that token.
     *
     * @param  mixed $value
     * @param  array $context
     * @return bool
     */
    public function isValid($value, array $context = null)
    {
        if (empty($value)) {
            $this->error(self::INVALID_FIELD); //@TO~DO~
            return false;
        }

        if (!isset($context[$this->getCompareTo()])) {
            $this->error(self::INVALID_FIELD); //@TO~DO~
            return false;
        }

        //  get compare to value
        if ($this->hasTime()) {
            $dateFilter = new DateTimeSelectNullifier();
            $dateFormat = self::DATETIME_FORMAT;
        } else {
            $dateFilter = new DateSelectNullifier();
            $dateFormat = self::DATE_FORMAT;
        }

        $compareToValue = $dateFilter->filter($context[$this->getCompareTo()]);

        if ($compareToValue === null) {
            $this->error(self::INVALID_FIELD); //@TO~DO~
            return false;
        }

        $compareDateValue = $this->generateCompareDateValue($compareToValue);

        if (count($this->getMessages()) !== 0) {
            // process any errors from sub class
            return false;
        }

        //  get date (time) value
        $dateValue = \DateTime::createFromFormat($dateFormat, $value);
        if (!$dateValue) {
            $this->error(self::NO_COMPARE); //@TO~DO~
            return false;
        }

        if (! $this->hasTime()) {
            $dateValue->setTime(0, 0, 0);
        }

        return $this->isValidForOperator($dateValue, $compareDateValue);
    }

    /**
     * Generate the compareDate value.
     *
     * @param $compareToValue
     * @return \DateTime
     */
    protected function generateCompareDateValue($compareToValue)
    {
        $dateFormat = ($this->hasTime() ? self::DATETIME_FORMAT : self::DATE_FORMAT);
        $compareDateValue = \DateTime::createFromFormat($dateFormat, $compareToValue);

        if (!$this->hasTime()) {
            $compareDateValue->setTime(0, 0, 0);
        }

        return $compareDateValue;
    }
}
