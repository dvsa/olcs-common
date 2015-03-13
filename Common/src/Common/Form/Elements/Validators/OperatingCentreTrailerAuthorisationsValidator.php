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
        'non-numeric' => 'OperatingCentreTrailerAuthorisationsValidator.non-numeric',
        'no-operating-centre' => 'OperatingCentreTrailerAuthorisationsValidator.no-operating-centre',
        '1-operating-centre' => 'OperatingCentreTrailerAuthorisationsValidator.1-operating-centre',
        'too-low' => 'OperatingCentreTrailerAuthorisationsValidator.too-low',
        'too-high' => 'OperatingCentreTrailerAuthorisationsValidator.too-high'
    );

    /**
     * Custom validation for trailer authorisations
     *
     * @param mixed $value
     * @param array $context
     */
    public function isValid($value, $context = null)
    {
        if (!is_null($value) && !is_numeric($value)) {
            $this->error('non-numeric');
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
