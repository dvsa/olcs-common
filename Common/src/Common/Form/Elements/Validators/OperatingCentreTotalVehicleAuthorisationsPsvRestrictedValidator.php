<?php

/**
 * Operating Centre Total Vehicle Authorisations Psv Restricted Validator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * Operating Centre Total Vehicle Authorisations Psv Restricted Validator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreTotalVehicleAuthorisationsPsvRestrictedValidator extends AbstractValidator
{
    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'restricted-too-many' => 'OperatingCentreVehicleAuthorisationsValidator.restricted-too-many'
    );

    /**
     * Custom validation for total vehicle authorisations
     *
     * @param mixed $value
     * @param array $context
     */
    public function isValid($value, $context = null)
    {
        if ($value > 2) {
            $this->error('restricted-too-many');
            return false;
        }

        return true;
    }
}
