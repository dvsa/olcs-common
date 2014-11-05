<?php

/**
 * OperatingCentreAuthorisationValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * OperatingCentreAuthorisationValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreAuthorisationValidator extends AbstractValidator
{
    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'too-low' => 'OperatingCentreVehicleAuthorisationValidator.too-low',
        'too-low-psv' => 'OperatingCentreVehicleAuthorisationValidator.too-low-psv'
    );

    /**
     * Custom validation for tachograph analyser
     *
     * @param mixed $value
     * @param array $context
     */
    public function isValid($value, $context = null)
    {
        $goods = false;
        $trailers = 0;

        if (isset($context['noOfTrailersRequired'])) {
            $trailers = (int)$context['noOfTrailersRequired'];
            $goods = true;
        }

        $total = (int)$context['noOfVehiclesRequired'] + $trailers;

        if ($total < 1) {
            $this->error('too-low' . (!$goods ? '-psv' : ''));
            return false;
        }

        return true;
    }
}
