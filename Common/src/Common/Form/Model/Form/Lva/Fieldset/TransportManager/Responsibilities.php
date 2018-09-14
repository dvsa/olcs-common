<?php

namespace Common\Form\Model\Form\Lva\Fieldset\TransportManager;

use Zend\Form\Annotation as Form;

/**
 * @Form\Attributes({"class":"table__form"})
 * @Form\Name("responsibilities")
 */
class Responsibilities
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
     * @Form\Options({
     *     "label": "transport-manager.responsibilities.tm-type",
     *     "category": "tm_type",
     *     "label_attributes": {"class": "form-control form-control--radio form-control--inline"}
     * })
     * @Form\Type("DynamicRadio")
     * @Form\Validator({
     *      "name":"Zend\Validator\NotEmpty"
     * })
     */
    public $tmType = null;

    /**
     * @Form\Options({
     *     "label": "transport-manager.responsibilities.tm-app-status",
     *     "disable_inarray_validator": false,
     *     "category": "tmap_status"
     * })
     * @Form\Required(true)
     * @Form\Attributes({"id":"","placeholder":"", "required":false})
     * @Form\Type("DynamicSelect")
     */
    public $tmApplicationStatus = null;

    /**
     * @Form\Options({
     *     "label": "transport-manager.responsibilities.is-owner",
     *     "value_options": {"Y":"Yes", "N":"No"},
     *     "label_attributes": {"class": "form-control form-control--radio form-control--inline"}
     * })
     * @Form\Type("Radio")
     */
    public $isOwner = null;

    /**
     * @Form\Name("hoursOfWeek")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\HoursOfWeekRequired")
     */
    public $hoursOfWeek = null;

    /**
     * @Form\Options({
     *     "label": "transport-manager.other-licence.form.radio.label",
     *     "hint" : "transport-manager.other-licence.form.radio.hint",
     *     "hint-class" : "",
     *     "value_options": {"Y":"Yes", "N":"No"},
     *     "label_attributes": {"class": "form-control form-control--radio form-control--inline"}
     * })
     * @Form\Type("Radio")
     */
    public $hasOtherLicences = null;

    /**
     * @Form\Name("otherLicences")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     * @Form\Attributes({
     *     "id":"otherLicences",
     *     "class": "help__text help__text--removePadding"
     * })
     */
    public $otherLicences = null;

    /**
     * @Form\Type("TextArea")
     * @Form\Attributes({
     *      "class":"long",
     *      "label": "transport-manager.responsibilities.additional-information.title",
     *      "autocomplete": "nope",
     * })
     * @Form\Options({
     *     "label": "transport-manager.responsibilities.additional-information",
     *     "label_options": {
     *         "disable_html_escape": "true"
     *     }
     * })
     * @Form\Required(false)
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({
     *      "name":"Zend\Validator\StringLength",
     *      "options":{
     *          "max":4000
     *      }
     * })
     */
    public $additionalInformation;

    /**
     * @Form\Attributes({"id":"file", "class": "file-upload"})
     * @Form\ComposedObject("\Common\Form\Model\Fieldset\MultipleFileUpload")
     */
    public $file = null;
}
