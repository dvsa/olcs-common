<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("interim")
 */
class Interim
{
    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *      "fieldset-attributes": {
     *          "class": "checkbox inline"
     *      },
     *     "label":
     * "interim.application.undertakings.form.checkbox.label",
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
    public $goodsApplicationInterim = null;

    /**
     * @Form\AllowEmpty(true)
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Required(true)
     * @Form\Type("TextArea")
     * @Form\Attributes({
     *     "required": false,
     *     "id": "applicationInterimReason",
     *     "class": "long js-interim-reason",
     * })
     * @Form\Options({
     *     "label": "interim.application.undertakings.form.textarea.placeholder",
     *     "label_attributes": {
     *         "id": "application-interim-reason"
     *     }
     * })
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({"name": "ValidateIf",
     *      "options":{
     *          "context_field": "goodsApplicationInterim",
     *          "context_values": {"Y"},
     *          "validators": {
     *              {
     *                  "name": "\Zend\Validator\NotEmpty",
     *                  "options": {
     *                      "message": "interim.application.undertakings.form.textarea.error.message.empty"
     *                  }
     *              }
     *          }
     *      }
     * })
     */
    public $goodsApplicationInterimReason = null;
}