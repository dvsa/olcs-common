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
        if ($value !== 'Y' && isset($context['psvOperateSmallVhl']) && $context['psvOperateSmallVhl'] === 'N') {
            $this->error('required');
            return false;
        }

        return true;
    }
}
