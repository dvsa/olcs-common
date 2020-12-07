<?php

namespace Common\Service\Qa;

use Laminas\Form\Fieldset;

interface FieldsetPopulatorInterface
{
    /**
     * Populate the supplied fieldset with form elements in accordance with the specified options
     *
     * @param mixed $form
     * @param Fieldset $fieldset
     * @param array $options
     */
    public function populate($form, Fieldset $fieldset, array $options);
}
