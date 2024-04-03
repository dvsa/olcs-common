<?php

/**
 * VehicleUndertakingsOperateSmallVehiclesAgreementValidator
 *
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */

namespace Common\Form\Elements\Validators;

use Laminas\Validator\AbstractValidator;

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
    protected $messageTemplates = [
        'required' => 'You must agree'
    ];

    /**
     * Custom validation for undertakings checkbox field
     *
     * @param mixed $value
     * @param array $context
     */
    public function isValid($value, $context = null)
    {
        // we can infer scottish rules based on whether or not the
        // small vehicles field exists. isset isn't enough because
        // it can exist but be null. If the field isn't present
        // *at all* then we're Scottish
        $isScotland = !array_key_exists('psvOperateSmallVhl', $context);

        if (
            ($isScotland || $context['psvOperateSmallVhl'] === 'N')
            && $value !== 'Y'
        ) {
            $this->error('required');
            return false;
        }

        return true;
    }
}
