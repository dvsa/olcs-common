<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Laminas\Form\Annotation as Form;

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
     * @Form\Required(false)
     * @Form\Options({
     *     "empty_option": "Please Select",
     *     "label": "application_your-business_people-sub-action-formTitle",
     *     "category":"person_title",
     * })
     * @Form\Type("DynamicSelect")
     */
    public $title = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({
     *     "label": "application_your-business_people-sub-action-formFirstName",
     * })
     * @Form\Type("Text")
     * @Form\Filter("Laminas\Filter\StringTrim")
     */
    public $forename = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({
     *     "label": "application_your-business_people-sub-action-formSurname",
     * })
     * @Form\Type("Text")
     * @Form\Filter("Laminas\Filter\StringTrim")
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
     *     "render_delimiters": true
     * })
     * @Form\Type("DateSelect")
     * @Form\Filter("DateSelectNullifier")
     * @Form\Validator("\Common\Validator\Date")
     * @Form\Validator("Date", options={"format":"Y-m-d"})
     */
    public $birthDate = null;
}
