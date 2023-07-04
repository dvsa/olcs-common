<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Laminas\Form\Annotation as Form;
use Common\Service\Helper\TranslationHelperService;

class AuthorityToOperate
{
    /**
     * @Form\Attributes({
     *     "value": "interim.application.undertakings.form.textarea.placeholder",
     *     "data-container-class": "typeOfLicence-guidance-restricted js-visible",
     *     "id": "application-interim-reason",
     * })
     * @Form\Options({"tokens":{"application_type-of-licence_licence-type.data.restrictedGuidance"}})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $interimGuidanceText = null;

/**
 * @Form\Required(true)
 * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
 * @Form\AllowEmpty(true)
 * @Form\Type("TextArea")
 * @Form\Attributes({
 *     "required": false,
 *     "id": "applicationInterimReason",
 *     "class": "long js-interim-reason",
 * })
 * @Form\Filter({"name":"Laminas\Filter\StringTrim"})
 */
    public $goodsApplicationInterimReason = null;
    /**
     * @Form\Attributes({"value": "markup-interim-fee","data-container-class": "interimFee", "id" : "interimFee"})
     * @Form\Type("\Common\Form\Elements\Types\GuidanceTranslated")
     */
    public $interimFee = null;

}
//    /**
//     * @Form\Name("interim")
//     * @Form\AllowEmpty(true)
//     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
//     * @Form\Required(true)
//     * @Form\Type("Textarea")
//     * @Form\Attributes({
//     *     "required": false,
//     *     "id": "applicationInterimReason",
//     *     "class": "long js-interim-reason",
//     * })
//     * @Form\Options({
//     *     "label": "interim.application.undertakings.form.textarea.placeholder",
//     *     "label_attributes": {
//     *         "id": "application-interim-reason"
//     *     }
//     * })
//     * @Form\Filter({"name":"Laminas\Filter\StringTrim"})
//     * @Form\Validator({"name": "ValidateIf",
//     *      "options":{
//     *          "context_field": "goodsApplicationInterim",
//     *          "context_values": {"Y"},
//     *          "validators": {
//     *              {
//     *                  "name": "\Laminas\Validator\NotEmpty",
//     *                  "options": {
//     *                      "message": "interim.application.undertakings.form.textarea.error.message.empty"
//     *                  }
//     *              }
//     *          }
//     *      }
//     * })
//     */