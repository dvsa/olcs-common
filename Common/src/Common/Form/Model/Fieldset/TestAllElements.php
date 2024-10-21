<?php

namespace Common\Form\Model\Fieldset;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Name("testAllElements")
 * @Form\Options({"label":"Test all form elements"})
 */
class TestAllElements
{
    /**
     * @Form\Options({
     *     "label": "text input",
     * })
     * @Form\Type("Text")
     */
    public $textInput;

    /**
     * @Form\Options({
     *     "label": "password input",
     * })
     * @Form\Type("Password")
     */
    public $passwordInput;

    /**
     * @Form\Type("TextArea")
     * @Form\Attributes({
     *      "label": "Text area input",
     * })
     * @Form\Options({
     *     "label": "Text area label 2",
     *     "label_options": {
     *         "disable_html_escape": "true"
     *     },
     *     "label_attributes": {"id":"additionalInformation"}
     * })
     */
    public $textArea;

    /**
     * @Form\Type("Common\Form\Elements\Types\RadioYesNo")
     * @Form\ErrorMessage("Yes-No error message")
     */
    public $radioYesNo;

    /**
     * @Form\Options({
     *     "label": "radio buttons with hints",
     *     "value_options":{
     *          "option1":"Option 1",
     *          "option2":"Option 2",
     *      },
     *      "value_option_hints":{
     *          "hint1":"Option 1",
     *          "hint2":"Option 2",
     *      },
     * })
     * @Form\Type("Radio")
     */
    public $radioWithHints;

    /**
     * @Form\Options({
     *     "label": "radio buttons without hints",
     *     "value_options":{
     *          "option1":"Option 1",
     *          "option2":"Option 2",
     *      },
     *     "value_option_hints":{
     *          "option1":"Hint for option 1",
     *          "option2":"Hint for option 2",
     *      },
     * })
     * @Form\Type("Radio")
     */
    public $radioWithoutHints;

    /**
     * @Form\Options({
     *     "label": "single checkbox",
     *     "checked_value":"Y",
     *     "unchecked_value":"N"
     * })
     * @Form\Type("OlcsCheckbox")
     */
    public $singleChecbox = null;

    /**
     * @Form\Options({
     *     "label": "Dynamic checkboxes (traffic areas)",
     *     "disable_inarray_validator": false,
     *     "service_name": "Common\Service\Data\TrafficArea",
     *     "exclude": {"N"},
     *     "use_groups": "false"
     * })
     * @Form\Type("DynamicMultiCheckbox")
     */
    public $dynamicMultiCheckbox;

    /**
     * @Form\Options({
     *     "label": "Dynamic select (traffic areas)",
     *     "empty_option": "All",
     *     "disable_inarray_validator": false,
     *     "service_name": "Common\Service\Data\TrafficArea",
     * })
     * @Form\Type("DynamicSelect")
     */
    public $dynamicSelect;
}
