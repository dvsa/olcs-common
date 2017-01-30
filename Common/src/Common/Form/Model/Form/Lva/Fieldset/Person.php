<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("data")
 */
class Person
{
    /**
     * @Form\Type("Hidden")
     * @Form\Attributes({"value":""})
     */
    public $id = null;

    /**
     * @Form\Type("Hidden")
     * @Form\Attributes({"value":""})
     */
    public $version = null;

    /**
     * @Form\Type("DynamicSelect")
     * @Form\Required(false)
     * @Form\Attributes({"id":"title","placeholder":""})
     * @Form\Options({
     *     "empty_option":"Please Select",
     *     "label":"application_your-business_people-sub-action-formTitle",
     *     "label_attributes":{"aria-label": "Select title"},
     *     "category":"person_title",
     * })
     */
    public $title = null;

    /**
     * @Form\Type("Text")
     * @Form\Attributes({"class":"long","id":"forename"})
     * @Form\Options({
     *     "label":"application_your-business_people-sub-action-formFirstName",
     *     "label_attributes": {"aria-label": "Enter first names"},
     *     "error-message": "person_forename-error"
     * })
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":0,"max":35}})
     * @Form\Validator({"name":"regex", 
     *     "options":{"pattern":"/^[a-z ,.'-]+$/i","messages":{"regexNotMatch":"error.characters.not-allowed"}}
     * })
     */
    public $forename = null;

    /**
     * @Form\Type("Text")
     * @Form\Attributes({"class":"long","id":"familyname"})
     * @Form\Options({
     *     "label":"application_your-business_people-sub-action-formSurname",
     *     "label_attributes": {"aria-label": "Enter last name"},
     *     "error-message": "person_familyName-error"
     * })
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":0,"max":35}})
     * @Form\Validator({"name":"regex", 
     *     "options":{"pattern":"/^[a-z ,.'-]+$/i","messages":{"regexNotMatch":"error.characters.not-allowed"}}
     * })
     */
    public $familyName = null;

    /**
     * @Form\Type("Text")
     * @Form\Required(false)
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({
     *     "label":"application_your-business_people-sub-action-formOtherNames",
     *     "label_attributes": {"aria-label": "Enter other names (optional)"}
     * })
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":0,"max":35}})
     * @Form\Validator({"name":"regex", 
     *     "options":{"pattern":"/^[a-z ,.'-]+$/i","messages":{"regexNotMatch":"error.characters.not-allowed"}}
     * })
     */
    public $otherName = null;

    /**
     * @Form\Type("Text")
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({"label":"application_your-business_people-sub-action-formPosition"})
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":0,"max":45}})
     */
    public $position = null;

    /**
     * @Form\Type("DateSelect")
     * @Form\Attributes({"id":"dob"})
     * @Form\Options({
     *     "label": "dob",
     *     "create_empty_option": false,
     *     "render_delimiters": "d m y",
     *     "error-message": "person_birthDate-error",
     *     "fieldset-attributes": {"id":"dob_day"}
     * })
     * @Form\Filter({"name": "DateSelectNullifier"})
     * @Form\Validator({"name": "\Common\Validator\Date"})
     * @Form\Validator({"name":"Date","options":{"format":"Y-m-d"}})
     * @Form\Validator({"name":"\Common\Form\Elements\Validators\DateNotInFuture"})
     */
    public $birthDate = null;
}
