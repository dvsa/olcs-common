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
        'notLessThan' => 'fees.foo.baz',
        'notLessThanInclusive' => 'fees.foo.baz',
    );
}
