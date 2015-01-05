<?php

/**
 * VehicleUndertakingsNoLimousineConfirmationValidator
 *
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * VehicleUndertakingsNoLimousineConfirmationValidator
 *
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */
class VehicleUndertakingsNoLimousineConfirmationValidator extends AbstractValidator
{
    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'required' => 'application_vehicle-safety_undertakings.limousines.required'
    );

    /**
     * Custom validation for undertakings checkbox field
     *
     * @param mixed $value
     * @param array $context
     */
    public function isValid($value, $context = null)
    {
        $requiredContext = $this->getOption('required_context_value');

        // This only gets used if psvOperateSmallVhl is shown
        if (isset($context['psvLimousines'])
            && $context['psvLimousines'] === $requiredContext
            && $value !== 'Y'
        ) {
            $this->error('required');

            return false;
        }

        return true;
    }
}
