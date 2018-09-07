<?php

namespace Common\Form\Model\Form\Lva\Fieldset\TransportManager;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("lva-transport-manager-details")
 * @Form\Attributes({"data-section":"details", "id":"details"})
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
     *     "short-label": "lva-tm-details-details-email"
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
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
     * @Form\Validator({"name": "\Zend\Validator\NotEmpty"})
     */
    public $birthPlace = null;

    /**
     * @Form\Attributes({"id":"certificate", "class": "file-upload"})
     * @Form\ComposedObject("\Common\Form\Model\Fieldset\MultipleFileUpload")
     * @Form\Options({
     *    "label":"lva-tm-details-details-certificate",
     *    "label_attributes": {
     *        "class": "legend",
     *        "aria-label": "Certificate of professional competence, attach file(s) for upload",
     *    },
     * })
     */
    public $certificate = null;
}
