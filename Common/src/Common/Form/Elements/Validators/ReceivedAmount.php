<?php

/**
 * Received Amount Validator
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Form\Elements\Validators;

/**
 * Received Amount Validator
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ReceivedAmount extends \Zend\Validator\Between
{
    const PART_PAYMENT_ERROR = 'partPaymentError';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_BETWEEN        => "The payment amount must be between %min% and %max%",
        self::NOT_BETWEEN_STRICT => "The payment amount must be between %min% and %max%",
        self::PART_PAYMENT_ERROR => "Part payments are permitted but the amount entered is insufficient to
            allocate any payment to one or more of the selected fees.",
    );

    public function isValid($value, $context = null)
    {
        if (isset($context['minAmountForValidator'])) {
            $this->setMin($context['minAmountForValidator']);
        }
        if (isset($context['maxAmountForValidator'])) {
            $this->setMax($context['maxAmountForValidator']);
        }
        $this->setInclusive(true);

        $valid = parent::isValid($value);

        if (!$valid) {
            $this->error(self::PART_PAYMENT_ERROR);
        }

        return $valid;
    }
}
