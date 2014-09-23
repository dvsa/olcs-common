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
    const INVALID_OPERATOR = 'invalid_operator';

    /**
     * Error messages
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_GTE => "This date must be after or the same as '%compare_to_label%'",
        self::NOT_GT => "This date must be after '%compare_to_label%'",
        self::NOT_LTE => "This date must be before or the same as '%compare_to_label%'",
        self::NOT_LT => "This date must be before '%compare_to_label%'",
        self::INVALID_OPERATOR => "Invalid operator'"
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
     * @param string $operator
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

        if (isset($option['operator'])) {
            $this->setOperator($options['operator']);
        }

        if (isset($options['compare_to_label'])) {
            $this->setCompareToLabel($options['compare_to_label']);
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
     * @throws Exception\RuntimeException if the token doesn't exist in the context array
     */
    public function isValid($value, array $context = null)
    {
        if (!isset($context[$this->getCompareTo()])) {
            $this->error('context field not in input'); //@TO~DO~
            return false;
        }

        $dateFilter = new DateSelectNullifier();
        $compareToValue = $dateFilter->filter($context[$this->getCompareTo()]);

        switch ($this->getOperator()) {
            case 'gte':
                if (!($value >= $compareToValue)) {
                    $this->error(self::NOT_GTE);
                    return false;
                }
                return true;
            case 'lte':
                if (!($value <= $compareToValue)) {
                    $this->error(self::NOT_LTE);
                    return false;
                }
                return true;
            case 'gt':
                if (!($value > $compareToValue)) {
                    $this->error(self::NOT_GT);
                    return false;
                }
                return true;
            case 'lt':
                if (!($value < $compareToValue)) {
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
