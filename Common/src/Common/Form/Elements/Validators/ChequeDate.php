<?php

/**
 * Checks a date for a cheque payment is valid
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator as AbstractValidator;

/**
 * Checks a date for a cheque payment is valid
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ChequeDate extends AbstractValidator
{
    /**
     * Error codes
     * @const string
     */
    const INVALID = 'invalid';

    /**
     * @const string
     */
    const MAX_INTERVAL = '+6 months';

    /**
     * Error messages
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID => "This date is not allowed to be more than six months in the future",
    );

    /**
     * @param  mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $date = strtotime($value);
        $limit = strtotime(self::MAX_INTERVAL);

        if ($date > $limit) {
            $this->error(self::INVALID);
            return false;
        }

        return true;
    }
}
