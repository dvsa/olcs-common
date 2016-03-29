<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * Business details allowEmail fieldset
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class BusinessDetailsAllowEmail
{

    /**
     * @Form\Type("Radio")
     * @Form\Options({
     *      "label": "How would you like to receive your correspondence?",
     *      "value_options":{
     *          "N":"Post",
     *          "Y":"Email"
     *      },
     * })
     */
    public $allowEmail;
}