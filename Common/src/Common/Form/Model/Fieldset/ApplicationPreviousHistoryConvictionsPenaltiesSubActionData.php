<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Name("data")
 */
class ApplicationPreviousHistoryConvictionsPenaltiesSubActionData
{

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $id = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label":
     * "selfserve-app-subSection-previous-history-criminal-conviction-formTitle",
     *     "value_options": {
     *         "Mr":
     * "selfserve-app-subSection-previous-history-criminal-conviction-formTitleValueMr",
     *         "Mrs":
     * "selfserve-app-subSection-previous-history-criminal-conviction-formTitleValueMrs",
     *         "Miss":
     * "selfserve-app-subSection-previous-history-criminal-conviction-formTitleValueMiss",
     *         "Ms":
     * "selfserve-app-subSection-previous-history-criminal-conviction-formTitleValueMs"
     *     },
     *     "empty_option": "Please Select",
     *     "disable_inarray_validator": false,
     *     "help-block": "Please select a category"
     * })
     * @Form\Type("\Zend\Form\Element\Select")
     */
    public $title = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({
     *     "label":
     * "selfserve-app-subSection-previous-history-criminal-conviction-formFirstName"
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $forename = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({
     *     "label":
     * "selfserve-app-subSection-previous-history-criminal-conviction-formLastName"
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $familyName = null;

    /**
     * @Form\Required(true)
     * @Form\Attributes({"id":"dob","required":false})
     * @Form\Options({
     *     "label":
     * "selfserve-app-subSection-previous-history-criminal-conviction-formDateOfConviction",
     *     "create_empty_option": true,
     *     "render_delimiters": false
     * })
     * @Form\Type("DateSelect")
     * @Form\Filter({"name": "DateSelectNullifier"})
     * @Form\Validator({"name": "Date", "options": {"format": "Y-m-d"}})
     */
    public $convictionDate = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({
     *     "label":
     * "selfserve-app-subSection-previous-history-criminal-conviction-formOffence"
     * })
     * @Form\Required(false)
     * @Form\Type("Text")
     */
    public $categoryText = null;

    /**
     * @Form\Attributes({"id":"","class":"long"})
     * @Form\Options({
     *     "label":
     * "selfserve-app-subSection-previous-history-criminal-conviction-formOffenceDetails",
     *     "label_attributes": {
     *         "class": "col-sm-2"
     *     },
     *     "column-size": "sm-6",
     *     "help-block":
     * "selfserve-app-subSection-previous-history-criminal-conviction-helpBlock",
     *     "hint":
     * "selfserve-app-subSection-previous-history-criminal-conviction-formOffenceDetaisHelpBlock"
     * })
     * @Form\Type("TextArea")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $notes = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({
     *     "label":
     * "selfserve-app-subSection-previous-history-criminal-conviction-formNameOfCourt"
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $courtFpn = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({
     *     "label":
     * "selfserve-app-subSection-previous-history-criminal-conviction-formPenalty"
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $penalty = null;


}

