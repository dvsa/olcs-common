<?php

namespace Common\Form\Model\Form\Lva\Fieldset\TransportManager;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Name("lva-transport-manager-details")
 * @Form\Attributes({"id":"details"})
 */
class Details
{
    /**
     * @Form\Options({"label":"lva-tm-details-details-name"})
     * @Form\Type("\Common\Form\Elements\Types\Readonly")
     */
    public $name = null;

    /**
     * @Form\Required(true)
     * @Form\Attributes({"id":"dob"})
     * @Form\Options({
     *     "label": "dob",
     *     "create_empty_option": true,
     *     "render_delimiters": false,
     *     "fieldset_attributes":{"id":"details[birthDate]"}
     * })
     * @Form\Type("DateSelect")
     * @Form\Filter({"name":"DateSelectNullifier"})
     * @Form\Validator({"name": "\Common\Validator\Date"})
     * @Form\Validator({"name":"Date","options":{"format":"Y-m-d"}})
     * @Form\Validator({"name":"\Common\Form\Elements\Validators\DateNotInFuture"})
     */
    public $birthDate = null;

    /**
     * @Form\Attributes({"class":"extra-long","id":"emailAddress"})
     * @Form\Options({
     *     "label":"lva-tm-details-details-email",
     *     "short-label": "lva-tm-details-details-email",
     *     "hint": "lva-tm-email-hint",
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Laminas\Filter\StringTrim"})
     * @Form\Validator({"name":"Dvsa\Olcs\Transfer\Validators\EmailAddress"})
     */
    public $emailAddress = null;

    /**
     * @Form\Attributes({"id":"birthPlace","class":"medium"})
     * @Form\Options({
     *     "label": "lva-tm-details-details-birthPlace",
     *     "short-label": "lva-tm-details-details-birthPlace",
     *     "label_attributes": {
     *         "aria-label": "Enter their place of birth"
     *     }
     * })
     * @Form\Type("Text")
     * @Form\Validator({"name": "\Laminas\Validator\NotEmpty"})
     */
    public $birthPlace = null;

    /**
     * @Form\Attributes({"value": "markup-lva-tm-details-details-certificateHtml"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $certificateHtml = null;

    /**
     * @Form\Attributes({"id":"certificate", "class": "file-upload"})
     * @Form\ComposedObject("\Common\Form\Model\Fieldset\MultipleFileUpload")
     * @Form\Options({
     *    "label":"lva-tm-details-details-certificate",
     *    "hint": "markup-professional-competence-certificates-obtained-abroad.phtml",
     *    "hint-position": "above",
     *    "label_attributes": {
     *        "class": "legend",
     *        "aria-label": "Certificate of professional competence, attach file(s) for upload",
     *    },
     * })
     */
    public $certificate = null;

    /**
     * @Form\Attributes({"value": "markup-lva-tm-details-details-lgvAcquiredRightsHtml"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $lgvAcquiredRightsHtml = null;

    /**
     * @Form\Required(false)
     * @Form\Type("Text")
     * @Form\Attributes({"id":"lgv-acquired-rights-ref-number","class":"medium"})
     * @Form\Options({
     *     "label": "lva-tm-details-details-lgvAcquiredRightsReferenceNumber",
     *     "label_attributes": {"class": "legend"},
     *     "hint": "lva-tm-details-details-lgvAcquiredRightsReferenceNumber-hint",
     * })
     * @Form\Filter({"name":"Laminas\Filter\StringTrim"})
     * @Form\Validator({
     *     "name": "\Laminas\Validator\StringLength",
     *     "options": {
     *         "min": 7,
     *         "max": 7,
     *         "messages": {
     *             \Laminas\Validator\StringLength::INVALID: "lva-tm-details-details-lgvAcquiredRightsReferenceNumber-error-length",
     *             \Laminas\Validator\StringLength::TOO_SHORT: "lva-tm-details-details-lgvAcquiredRightsReferenceNumber-error-length",
     *             \Laminas\Validator\StringLength::TOO_LONG: "lva-tm-details-details-lgvAcquiredRightsReferenceNumber-error-length",
     *         }
     *     }
     * })
     */
    public $lgvAcquiredRightsReferenceNumber = null;

    /**
     * @Form\Options({
     *     "label": "tm-review-responsibility-training-undertaken",
     *     "value_options": {"Y":"Yes", "N":"No"},
     *     "label_attributes": {"class": "form-control form-control--radio form-control--inline"},
     *     "hint" : "tm-hint-responsibility-training-undertaken",
     *     "hint-position" : "below",
     *     "hint-class" : "govuk-radios__conditional govuk-body hint hint__below hint__black hintNoTraining",
     * })
     * @Form\Type("Radio")
     */
    public $hasUndertakenTraining = null;
}
