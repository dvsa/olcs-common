<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("transport-consultant")
 * @Form\Attributes({"class":""})
 */
class TransportConsultant
{
    /**
     * @Form\Name("add-transport-consultant")
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *      "fieldset-attributes": {
     *          "class": "checkbox inline"
     *      },
     *     "label":
     * "application_your-business_business-type.add-transport-consultant.label",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Attributes({
     *     "value": "N"
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $addTransportConsultant = null;

    /**
     * @Form\Type("OlcsCheckbox")
     * @Form\Attributes({"id":"written-permission-to-engage","placeholder":""})
     * @Form\Options({
     *     "label": "application_your-business_business-type.written-perm-engage.label",
     *     "checked_value":"Y",
     *     "unchecked_value":"N",
     * })
     */
    public $writtenPermissionToEngage = null;

    /**
     * @Form\Type("Text")
     * @Form\Attributes({"class":"long"})
     * @Form\Options({"label":"application_your-business_business-type.consultant-name.label"})
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $transportConsultantName = null;

    /**
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Address")
     */
    public $address = null;

    /**
     * @Form\ComposedObject("Common\Form\Model\Fieldset\ContactOptional")
     */
    public $contact = null;
}
