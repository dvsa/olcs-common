<?php

namespace Common\Service\Qa;

use Zend\Form\Fieldset;

class FieldsetFactory
{
    /**
     * Create a fieldset with the supplied name
     *
     * @param string $name
     *
     * @return Fieldset
     */
    public function create($name)
    {
        return new Fieldset($name);
    }
}
