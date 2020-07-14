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
        $radioVerticalTypes = [
            'ecmt_st_restricted_countries',
            'bilateral_cabotage_only',
            'bilateral_standard_and_cabotage',
            'bilateral_third_country',
        ];

        if (in_array($type, $radioVerticalTypes)) {
            return new RadioVertical($name);
        }

        return new Fieldset($name);
    }
}
