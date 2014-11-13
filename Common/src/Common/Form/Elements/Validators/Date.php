<?php

/**
 * Override ZFs date validation messages (As they are a bit rubbish)
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\Date as ZendDate;

/**
 * Override ZFs date validation messages (As they are a bit rubbish)
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Date extends ZendDate
{

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID        => "Please select a date",
        self::INVALID_DATE   => "The input does not appear to be a valid date",
        self::FALSEFORMAT    => "The input does not fit the date format '%format%'",
    );

    /**
     * Returns true if $value is a DateTime instance or can be converted into one.
     * Returns false if $value is empty of invalid  and gives an error message
     *
     * @param  string|array|int|DateTime $value
     * @return bool
     */
    public function isValid($value)
    {
        $this->setValue($value);

        if (empty($value)) {
            $this->error(self::INVALID);
            return false;
        }

        if (!$this->convertToDateTime($value, false)) {
            $this->error(self::INVALID_DATE);
            return false;
        }

        return true;
    }
}
