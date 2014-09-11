<?php

/**
 * Ensure the VRM is NOT in the list of VRMS provided
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * Ensure the VRM is NOT in the list of VRMS provided
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class NewVrm extends AbstractValidator
{
    /**
     * Holds the Vrms to check against
     *
     * @var array
     */
    private $vrms = array();

    /**
     * Holds the type
     *
     * @var string
     */
    private $type = '';

    /**
     * Holds the templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'not-unique-Licence' => 'error.vehicle.vrm-exists-on-licence',
        'not-unique-Application' => 'error.vehicle.vrm-exists-on-application'
    );

    /**
     * Set the type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Set Vrms
     *
     * @param array $vrms
     */
    public function setVrms(array $vrms = array())
    {
        $this->vrms = $vrms;
    }

    /**
     * Check if VRM is valid
     *
     * @param string $value
     */
    public function isValid($value)
    {
        if (in_array($value, $this->vrms)) {

            $this->error('not-unique-' . $this->type);

            return false;
        }

        return true;
    }
}
