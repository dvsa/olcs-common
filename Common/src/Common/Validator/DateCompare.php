<?php

namespace Common\Validator;

use Common\Filter\DateSelectNullifier;
use Zend\Validator\AbstractValidator;

/**
 * Class DateCompare - used to validate two dates via a legal operator:
 * 'gt' -> greater than
 * 'gte' -> greater than or equal to
 * 'lt' -> less than
 * 'lte' -> less than or equal to
 * @package Common\Validator
 */
class DateCompare extends AbstractValidator
{
    /**
     * Error codes
     * @const string
     */
    const NOT_GTE = 'notGreaterThanOrEqual';
    const NOT_GT = 'notGreaterThan';
    const NOT_LTE = 'notLessThanOrEqual';
    const NOT_LT = 'notLessThan';
    const INVALID_OPERATOR = 'invalidOperator';
    const INVALID_FIELD = 'invalidField';

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
        self::INVALID_FIELD => "Input field being compared to doesn't exist"
    );

    /**
     * @var array
     */
    protected $messageVariables = array(
        'compare_to_label' => 'compareToLabel'
    );

    /**
     * context field against which to validate
     * @var string
     */
    protected $compareTo;

    /**
     * Type of compare to do
     * @var string
     */
    protected $operator;

    /**
     * Label of compare to field to use in error message
     * @var string
     */
    protected $compareToLabel;

    /**
     * Whether we're comparing the time also
     * @var string
     */
    protected $hasTime;

    /**
     * @param string $compareTo
     * @return $this
     */
    public function setCompareTo($compareTo)
    {
        $this->compareTo = $compareTo;
        return $this;
    }

    /**
     * @return string
     */
    public function getCompareTo()
    {
        return $this->compareTo;
    }

    /**
     * @param string $compareToLabel
     * @return $this
     */
    public function setCompareToLabel($compareToLabel)
    {
        $this->compareToLabel = $compareToLabel;
        return $this;
    }

    /**
     * @return string
     */
    public function getCompareToLabel()
    {
        return $this->compareToLabel;
    }

    /**
     * @param string $hasTime
     * @return $this
     */
    public function setHasTime($hasTime)
    {
        $this->hasTime = $hasTime;
        return $this;
    }

    /**
     * @return bool
     */
    public function getHasTime()
    {
        return $this->hasTime;
    }

    /**
     * @param bool $operator
     * @return $this
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
        return $this;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    public function setOptions($options = array())
    {
        if (isset($options['compare_to'])) {
            $this->setCompareTo($options['compare_to']);
        }

        if (isset($options['operator'])) {
            $this->setOperator($options['operator']);
        }

        if (isset($options['compare_to_label'])) {
            $this->setCompareToLabel($options['compare_to_label']);
        }

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

        $dateFilter = new DateSelectNullifier();
        $compareToValue = $dateFilter->filter($context[$this->getCompareTo()]);

        if (is_null($compareToValue)) {
            $this->error(self::INVALID_FIELD); //@TO~DO~
            return false;
        }

        $compareDateValue = \DateTime::createFromFormat('Y-m-d', $compareToValue);
        $compareDateValue->setTime(0, 0, 0);

        //if we're comparing a field which also has a time
        if ($this->getHasTime()) {
            $dateValue = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
        } else {
            $dateValue = \DateTime::createFromFormat('Y-m-d', $value);
        }

        $dateValue->setTime(0, 0, 0);

        switch ($this->getOperator()) {
            case 'gte':
                if (!($dateValue >= $compareDateValue)) {
                    $this->error(self::NOT_GTE);
                    return false;
                }
                return true;
            case 'lte':
                if (!($dateValue <= $compareDateValue)) {
                    $this->error(self::NOT_LTE);
                    return false;
                }
                return true;
            case 'gt':
                if (!($dateValue > $compareDateValue)) {
                    $this->error(self::NOT_GT);
                    return false;
                }
                return true;
            case 'lt':
                if (!($dateValue < $compareDateValue)) {
                    $this->error(self::NOT_LT);
                    return false;
                }
                return true;
            default:
                $this->error(self::INVALID_OPERATOR);
                return false;
        }
    }
}
