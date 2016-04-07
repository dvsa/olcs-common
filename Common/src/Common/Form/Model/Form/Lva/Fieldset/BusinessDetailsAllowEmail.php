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
     * @Form\Attributes({"id":"","placeholder":"","class":"js-enabled"})
     * @Form\Options({
     *     "fieldset-attributes": {
     *         "class": "checkbox inline"
     *     },
     *     "label": "application_business-details_allow-email.label",
     *     "value_options": {
     *         {
     *             "value": "N",
     *             "label": "Post"
     *         },
     *         {
     *             "value": "Y",
     *             "label": "Email",
     *         },
     *     },
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $allowEmail;
}
