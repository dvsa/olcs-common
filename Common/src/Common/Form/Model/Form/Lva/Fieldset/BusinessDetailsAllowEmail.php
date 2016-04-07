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
     *      "label": "application_business-details_allow-email.label",
     *      "value_options":{
     *          "N":"Post",
     *          "Y":"Email"
     *      },
     * })
     */
    public $allowEmail;
}
