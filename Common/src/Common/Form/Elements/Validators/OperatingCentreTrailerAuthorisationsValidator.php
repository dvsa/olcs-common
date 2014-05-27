<?php

/**
 * OperatingCentreTrailerAuthorisationsValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * OperatingCentreTrailerAuthorisationsValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreTrailerAuthorisationsValidator extends AbstractValidator
{
    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'none-numeric' => 'Please enter a numeric value',
        'no-operating-centre' => 'Please add an operating centre before setting the total number of trailers',
        '1-operating-centre' => 'If you are only applying for one operating centre, the total number of authorised trailers must be the same as at your operating centre',
        'too-low' => 'The total number of authorised trailers must be equal or greater than the largest number of trailers authorised at any individual operating centre',
        'too-high' => 'The number of authorised trailers must not exceed the total number of trailers parked across all of your operating centres'
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

        if ($noOfOperatingCentres === 1 && $value != $context['minTrailerAuth']) {

            $this->error('1-operating-centre');
            return false;
        }

        $valid = true;

        if ($noOfOperatingCentres >= 2) {

            if ($value < $context['minTrailerAuth']) {

                $valid = false;
                $this->error('too-low');
            }

            if ($value > $context['maxTrailerAuth']) {

                $valid = false;
                $this->error('too-high');
            }
        }

        return $valid;
    }
}
