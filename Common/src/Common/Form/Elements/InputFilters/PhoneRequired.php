<?php

namespace Common\Form\Elements\InputFilters;

use Laminas\Validator\NotEmpty;

/**
 * Phone Required Filter
 */
class PhoneRequired extends Phone
{
    protected $required = true;

    /**
     * Initialise the form
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->setLabel('contact-number');
    }
}
