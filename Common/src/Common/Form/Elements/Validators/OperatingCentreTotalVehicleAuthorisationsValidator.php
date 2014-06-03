<?php

/**
 * OperatingCentreTotalVehicleAuthorisationsValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * OperatingCentreTotalVehicleAuthorisationsValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreTotalVehicleAuthorisationsValidator extends AbstractValidator
{
    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'none-numeric' => 'OperatingCentreVehicleAuthorisationsValidator.none-numeric',
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

        $noOfOperatingCentres = (int)$context['noOfOperatingCentres'];
        $value = (int)$value;

        if ($noOfOperatingCentres === 0 && $value !== 0) {

            $this->error('no-operating-centre');
            return false;
        }

        if ($noOfOperatingCentres === 1 && $value != $context['minVehicleAuth']) {

            $this->error('1-operating-centre');
            return false;
        }

        $valid = true;

        if ($noOfOperatingCentres >= 2) {

            if ($value < $context['minVehicleAuth']) {

                $valid = false;
                $this->error('too-low');
            }

            if ($value > $context['maxVehicleAuth']) {

                $valid = false;
                $this->error('too-high');
            }
        }

        return $valid;
    }
}
