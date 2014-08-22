<?php

/**
 * VehiclesUndertakingsOperateSmallVehicles
 *
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * VehiclesUndertakingsOperateSmallVehiclesValidator
 *
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */
class VehicleUndertakingsOperateSmallVehiclesValidator extends AbstractValidator
{
    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'required' => 'Value is required and can\'t be empty'
    );

    /**
     * Custom validation for undertakings text field
     *
     * @param mixed $value
     * @param array $context
     */
    public function isValid($value, $context = null)
    {
        unset($value);

        // This only gets used if psvOperateSmallVehicles is shown
        if ( isset($context['psvOperateSmallVhl']) ) {
            if ($context['psvOperateSmallVhl'] === 'Y'
                && trim($context['psvSmallVhlNotes']) === '') {

                $this->error('required');

                return false;
            }
        }

        return true;
    }
}
