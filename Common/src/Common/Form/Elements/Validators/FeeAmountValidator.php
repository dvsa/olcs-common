<?php

/**
 * Fee Amount Validator
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\LessThan;

/**
 * Fee Amount Validator
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class FeeAmountValidator extends LessThan
{
    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'notLessThan' => 'fees.amount_too_large',
        'notLessThanInclusive' => 'fees.amount_too_large',
    );
}
