<?php

namespace Common\Service\Qa\FieldsetModifier;

use Zend\Form\Fieldset;

class RoadworthinessMakeAndModelFieldsetModifier implements FieldsetModifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function shouldModify(Fieldset $fieldset)
    {
        $eligibleFieldsetNames = [
            Fieldsets::ROADWORTHINESS_VEHICLE_MAKE_AND_MODEL,
            Fieldsets::ROADWORTHINESS_TRAILER_MAKE_AND_MODEL,
        ];

        return in_array(
            $fieldset->getName(),
            $eligibleFieldsetNames
        );
    }

    /**
     * {@inheritdoc}
     */
    public function modify(Fieldset $fieldset)
    {
        $qaElement = $fieldset->get('qaElement');

        $qaElement->setAttribute(
            'class',
            'govuk-input govuk-input--width-50'
        );
    }
}
