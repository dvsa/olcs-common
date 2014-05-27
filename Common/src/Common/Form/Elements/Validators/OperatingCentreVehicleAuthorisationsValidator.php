<?php

/**
 * OperatingCentreVehicleAuthorisationsValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * OperatingCentreVehicleAuthorisationsValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreVehicleAuthorisationsValidator extends AbstractValidator
{
    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'none-numeric' => 'OperatingCentreVehicleAuthorisationsValidator.none-numeric',
        'restricted-too-many' => 'OperatingCentreVehicleAuthorisationsValidator.restricted-too-many',
        'no-vehicle-types' => 'OperatingCentreVehicleAuthorisationsValidator.no-vehicle-types',
        'no-operating-centre' => 'OperatingCentreVehicleAuthorisationsValidator.no-operating-centre',
        '1-operating-centre' => 'OperatingCentreVehicleAuthorisationsValidator.1-operating-centre',
        'too-low' => 'OperatingCentreVehicleAuthorisationsValidator.too-low',
        'too-high' => 'OperatingCentreVehicleAuthorisationsValidator.too-high'
    );

    /**
     * Custom validation for tachograph analyser
     *
     * @param mixed $value
     * @param array $context
     */
    public function isValid($value, $context = null)
    {
        if (!is_numeric($value)) {
            $this->error('none-numeric');
            return false;
        }

        $total = 0;
        $total += (isset($context['totAuthSmallVehicles']) ? $context['totAuthSmallVehicles'] : 0);
        $total += (isset($context['totAuthMediumVehicles']) ? $context['totAuthMediumVehicles'] : 0);
        $total += (isset($context['totAuthLargeVehicles']) ? $context['totAuthLargeVehicles'] : 0);

        if (isset($context['licenceType']) && $context['licenceType'] == 'restricted' && $total > 2) {
            $this->error('restricted-too-many');
            return false;
        }

        if ($context['noOfOperatingCentres'] === 0) {
            $this->error('no-operating-centre');
            return false;
        }

        if ($total == 0) {
            $this->error('no-vehicle-types');
            return false;
        }

        if ($context['noOfOperatingCentres'] === 1 && $total != $context['minVehicleAuth']) {

            $this->error('1-operating-centre');
            return false;
        }

        if ($context['noOfOperatingCentres'] >= 2) {

            if ($total < $context['minVehicleAuth']) {
                $this->error('too-low');
                return false;
            }

            if ($total > $context['maxVehicleAuth']) {
                $this->error('too-high');
                return false;
            }
        }

        return true;
    }
}
