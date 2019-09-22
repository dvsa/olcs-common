<?php

namespace Common\Service\Qa;

use Common\Form\Elements\Types\RadioVertical;
use Zend\Form\Fieldset;

class FieldsetFactory
{
    /**
     * Create a fieldset with the supplied name
     *
     * @param string $name
     * @param string $type
     *
     * @return mixed
     */
    public function create($name, $type)
    {
        if ($type == 'ecmt_st_restricted_countries') {
            return new RadioVertical($name);
        }

        return new Fieldset($name);
    }
}
