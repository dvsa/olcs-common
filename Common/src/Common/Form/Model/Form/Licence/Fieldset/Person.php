<?php

namespace Common\Form\Model\Form\Licence\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * Class Person
 *
 * @package Common\Form\Model\Form\Licence\Fieldset
 *
 *
 */
class Person
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
     * @Form\Attributes({"id":"title","placeholder":""})
     * @Form\Options({
     *     "empty_option": "Please Select",
     *     "label": "Title",
     *     "label_attributes": {"class": "form-element__question"},
     *     "category":"person_title",
     * })
     * @Form\Type("DynamicSelect")
     * @Form\Required(true)
     */
    public $title = null;

    /**
     * @Form\Attributes({"class":"long","id":"forename"})
     * @Form\Options({
     *     "label":"First name",
     *     "label_attributes": {"class": "form-element__question"},
     *     "error-message": "Enter first name"
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":0,"max":35}})
     */
    public $forename = null;

    /**
     * @Form\Attributes({"class":"long","id":"familyname"})
     * @Form\Options({
     *    "label":"Last name",
     *     "label_attributes": {"class": "form-element__question"},
     *     "error-message": "Enter last name"
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":0,"max":35}})
     */
    public $familyName = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({
     *    "label":"Other names (optional)",
     *     "label_attributes": {"class": "form-element__question"}
     * })
     * @Form\Required(false)
     * @Form\Type("Text")
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":0,"max":35}})
     */
    public $otherName = null;

    /**
     * @Form\Required(true)
     * @Form\Attributes({"id":"dob"})
     * @Form\Options({
     *     "label": "Date of Birth",
     *     "create_empty_option": true,
     *     "render_delimiters": false,
     *     "error-message": "Enter date of birth"
     * })
     * @Form\Type("DateSelect")
     * @Form\Filter({"name": "DateSelectNullifier"})
     * @Form\Validator({"name": "\Common\Validator\Date"})
     * @Form\Validator({"name":"Date","options":{"format":"Y-m-d"}})
     * @Form\Validator({"name": "\Common\Form\Elements\Validators\DateNotInFuture"})
     */
    public $birthDate = null;
}
