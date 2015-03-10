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

    const MAX_INTERVAL = '+6 months';

    /**
     * @var int
     */
    protected $dateLimit;

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
        $limit = $this->getDateLimit();

        if ($date > $limit) {
            $this->error(self::INVALID);
            return false;
        }

        return true;
    }

    /**
     * @return int
     */
    public function getDateLimit()
    {
        if (is_null($this->dateLimit)) {
            $this->dateLimit = strtotime(self::MAX_INTERVAL);
        }
        return $this->dateLimit;
    }

    /**
     * Setter is mainly for unit testing
     *
     * @param int $limit timestamp
     * @return self
     */
    public function setDateLimit($limit)
    {
        $this->dateLimit = $limit;
        return $this;
    }

}
