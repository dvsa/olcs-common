<?php

/**
 * Additional Psv Discs Validator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * Additional Psv Discs Validator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AdditionalPsvDiscsValidator extends AbstractValidator
{
    protected $messageTemplates = array(
        'too-many' => 'additional-psv-discs-validator-too-many'
    );

    public function isValid($value, $context = null)
    {
        $max = (int)$context['totalAuth'];

        $newCount = (int)$value + (int)$context['discCount'];

        if ($newCount > $max) {
            $this->error('too-many');
            return false;
        }

        return true;
    }
}
