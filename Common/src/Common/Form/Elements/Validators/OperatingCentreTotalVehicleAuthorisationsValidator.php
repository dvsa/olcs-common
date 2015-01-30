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

        return $this->checkMultipleOperatingCentresValidation($noOfOperatingCentres, $value, $context);
    }

    /**
     * Had to split this into a separate method as NPath was 60000, (Could split this into 3 separate validators in
     * the future?)
     *
     * @param int $noOfOperatingCentres
     * @param int $value
     * @param array $context
     * @return boolean
     */
    private function checkMultipleOperatingCentresValidation($noOfOperatingCentres, $value, $context)
    {

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
