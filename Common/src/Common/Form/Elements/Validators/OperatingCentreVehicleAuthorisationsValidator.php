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
        'none-numeric' => 'Please enter a numeric value',
        'restricted-too-many' => 'The total number of vehicles on a restricted licence cannot exceed 2',
        'no-vehicle-types' => 'You must enter at least 1 vehicle type',
        'no-operating-centre' => 'Please add an operating centre before setting the total number of vehicles',
        '1-operating-centre' => 'If you are only applying for one operating centre, the total number of authorised vehicles must be the same as at your operating centre',
        'too-low' => 'The total number of authorised vehicles must be equal or greater than the largest number of vehicles authorised at any individual operating centre',
        'too-high' => 'The number of authorised vehicles must not exceed the total number of vehicles parked across all of your operating centres'
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

        if (isset($context['licenceType'])
            && $context['licenceType'] == 'restricted' && ($context['totAuthSmallVehicles'] + $context['totAuthMediumVehicles']) > 2) {
            $this->error('restricted-too-many');
            return false;
        }

        $noOfOperatingCentres = (int)$context['noOfOperatingCentres'];
        $value = (int)$value;

        if ($noOfOperatingCentres === 0 && $value !== 0) {

            $this->error('no-operating-centre');
            return false;
        }

        $total = 0;

        $total += (isset($context['totAuthSmallVehicles']) ? $context['totAuthSmallVehicles'] : 0);
        $total += (isset($context['totAuthMediumVehicles']) ? $context['totAuthMediumVehicles'] : 0);
        $total += (isset($context['totAuthLargeVehicles']) ? $context['totAuthLargeVehicles'] : 0);

        if ($total == 0) {
            $this->error('no-vehicle-types');
            return false;
        }

        if ($noOfOperatingCentres === 1 && $total != $context['minVehicleAuth']) {

            $this->error('1-operating-centre');
            return false;
        }

        $valid = true;

        if ($noOfOperatingCentres >= 2) {

            if ($total < $context['minVehicleAuth']) {

                $valid = false;
                $this->error('too-low');
            }

            if ($total > $context['maxVehicleAuth']) {

                $valid = false;
                $this->error('too-high');
            }
        }

        return $valid;
    }
}
