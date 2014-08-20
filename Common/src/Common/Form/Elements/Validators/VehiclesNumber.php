<?php

/**
 * VehiclesNumber Validator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * VehiclesNumber Validator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VehiclesNumber extends AbstractValidator
{
    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'noOfVehiclesPossessed' => 'noOfVehiclesPossessedError',
        'noOfVehiclesPossessed-psv' => 'noOfVehiclesPossessedError-psv',
        'noOfTrailersPossessed' => 'noOfTrailersPossessedError'
    );

    /**
     * Holds the name
     *
     * @var string
     */
    private $name;

    /**
     * Pass in the element name
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        parent::__construct(array());
    }

    /**
     * Custom validation for tachograph analyser
     *
     * @param mixed $value
     * @param array $context
     */
    public function isValid($value, $context = null)
    {
        if ($value > 0) {
            return true;
        }

        $total = 0;

        $total += (isset($context['noOfVehiclesPossessed']) ? $context['noOfVehiclesPossessed'] : 0);

        $total += (isset($context['noOfTrailersPossessed']) ? $context['noOfTrailersPossessed'] : 0);

        if ($total < 1) {

            if (!isset($context['noOfTrailersPossessed'])) {
                $this->error($this->name . '-psv');
                return false;
            }

            $this->error($this->name);
            return false;
        }

        return true;
    }
}
