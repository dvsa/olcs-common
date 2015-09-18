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
    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_BETWEEN => "Part payments are permitted but the amount entered is insufficient to
            allocate any payment to one or more of the selected fees.",
        self::NOT_BETWEEN_STRICT => "The input is not strictly between '%min%' and '%max%'"
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

        return parent::isValid($value);
    }
}
