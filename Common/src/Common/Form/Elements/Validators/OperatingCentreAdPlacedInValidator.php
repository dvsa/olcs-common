<?php

/**
 * OperatingCentreAdPlacedInValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * OperatingCentreAdPlacedInValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreAdPlacedInValidator extends AbstractValidator
{
    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'required' => 'OperatingCentreAdPlacedInValidator.required'
    );

    /**
     * Custom validation for tachograph analyser
     *
     * @param mixed $value
     * @param array $context
     */
    public function isValid($value, $context = null)
    {
        if ($context['adPlaced'] != 'Y') {
            return true;
        }

        if (trim($value) == '') {
            $this->error('required');
            return false;
        }

        return true;
    }
}
