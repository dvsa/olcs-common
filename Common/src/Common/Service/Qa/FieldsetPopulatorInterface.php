<?php

namespace Common\Service\Qa;

use Zend\Form\Fieldset;

interface FieldsetPopulatorInterface
{
    public function populate(Fieldset $fieldset, array $options);
}
