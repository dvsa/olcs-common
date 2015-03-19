<?php

/**
 * VehicleUndertakingsOperateSmallVehiclesAgreementValidator
 *
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * VehicleUndertakingsOperateSmallVehiclesAgreementValidator
 *
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */
class VehicleUndertakingsOperateSmallVehiclesAgreementValidator extends AbstractValidator
{
    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'required' => 'You must agree'
    );

    /**
     * Custom validation for undertakings checkbox field
     *
     * @param mixed $value
     * @param array $context
     */
    public function isValid($value, $context = null)
    {
        unset($value);

        // This only gets used if psvOperateSmallVhl is shown
        if (array_key_exists('psvOperateSmallVhl', $context)) {

            if ($context['psvOperateSmallVhl'] === 'N' && $context['psvSmallVhlConfirmation'] !== 'Y') {
                $this->error('required');
                return false;
            }

        } else {
            // Scotland
            if (isset($context['psvSmallVhlScotland'])) {
                if ($context['psvSmallVhlConfirmation'] !== 'Y') {
                    $this->error('required');
                    return false;
                }
            }

        }

        return true;
    }
}
