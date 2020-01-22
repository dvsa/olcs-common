<?php

namespace Common\Service\Qa\FieldsetModifier;

use Zend\Form\Fieldset;

interface FieldsetModifierInterface
{
    /**
     * Whether the specified fieldset needs to be modified by this fieldset modifier
     *
     * @param Fieldset $fieldset
     *
     * @return bool
     */
    public function shouldModify(Fieldset $fieldset);

    /**
     * Make the required changes to the fieldset when shouldModify has returned true
     *
     * @param Fieldset $fieldset
     */
    public function modify(Fieldset $fieldset);
}
