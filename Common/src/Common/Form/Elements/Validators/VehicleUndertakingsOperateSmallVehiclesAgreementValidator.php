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

        var_dump($context);

        // This only gets used if psvOperateSmallVehicles is shown
        if ( isset($context['psvOperateSmallVehicles']) ) {
            if ($context['psvOperateSmallVehicles'] === 'N'
                && $context['psvSmallVehicleConfirmation'] !== '1') {

                $this->error('required');

                return false;
            }
        }

        return true;
    }
}
