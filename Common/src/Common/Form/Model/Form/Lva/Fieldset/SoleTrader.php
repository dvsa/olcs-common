<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("data")
 */
class SoleTrader
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
     *     "label": "application_your-business_people-sub-action-formTitle",
     *     "value_options": {
     *         "Mr": "application_your-business_people-sub-action-formTitleValueMr",
     *         "Mrs": "application_your-business_people-sub-action-formTitleValueMrs",
     *         "Miss":
     * "application_your-business_people-sub-action-formTitleValueMiss",
     *         "Ms": "application_your-business_people-sub-action-formTitleValueMs"
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
     * @Form\Options({"label":"application_your-business_people-sub-action-formFirstName"})
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $forename = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({"label":"application_your-business_people-sub-action-formSurname"})
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $familyName = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({
     *     "label":"application_your-business_people-sub-action-formOtherNames",
     *     "hint":"application_your-business_people-sub-action-formOtherNames-hint"
     * })
     * @Form\Required(false)
     * @Form\Type("Text")
     */
    public $otherName = null;

    /**
     * @Form\Attributes({"id":"dob"})
     * @Form\Options({
     *     "label": "application_your-business_people-sub-action-formDateOfBirth",
     *     "create_empty_option": false,
     *     "render_delimiters": "d m y"
     * })
     * @Form\Type("\Zend\Form\Element\DateSelect")
     * @Form\Filter({"name": "DateSelectNullifier"})
     * @Form\Validator({"name": "Date", "options": {"format": "Y-m-d"}})
     */
    public $birthDate = null;
}
