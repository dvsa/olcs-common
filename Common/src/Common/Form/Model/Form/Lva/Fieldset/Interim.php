<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Name("interim")
 */
class Interim
{
    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "interim.application.undertakings.form.checkbox.label",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "label_attributes": {"class": "form-control form-control--radio form-control--inline"}
     * })
     * @Form\Attributes({"value": "N"})
     * @Form\Type("\Laminas\Form\Element\Radio")
     */
    public $goodsApplicationInterim = null;

    /**
     * @Form\Attributes({"value": "markup-interim-fee", "data-container-class": "interimFee"})
     * @Form\Type("\Common\Form\Elements\Types\GuidanceTranslated")
     */
    public $interimFee = null;

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
     * @Form\Filter({"name":"Laminas\Filter\StringTrim"})
     * @Form\Validator({"name": "ValidateIf",
     *      "options":{
     *          "context_field": "goodsApplicationInterim",
     *          "context_values": {"Y"},
     *          "validators": {
     *              {
     *                  "name": "\Laminas\Validator\NotEmpty",
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
