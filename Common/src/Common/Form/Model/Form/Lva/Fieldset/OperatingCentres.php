<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({})
 */
class OperatingCentres
{
    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\Type("Text")
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\AllowEmpty(true)
     * @Form\Required(true)
     * @Form\Attributes({"class":"short","id":"totAuthVehicles","required":false,"pattern":"\d*"})
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation.data.totAuthVehicles",
     *     "label_attributes": {"class": "form-element__question"},
     *     "short-label": "totAuthVehicles",
     *     "error-message": "operatingCentres_totAuthVehicles-error",
     *     "hint": "markup-operating-centres-authorisation",
     *     "hint-position": "below"
     * })
     * @Form\Validator({"name": "Digits"})
     * @Form\Validator({"name": "Between", "options": {"min":1, "max": 1000000}})
     * @Form\Filter({"name":"\Zend\Filter\Null", "options":{"type":"string"}})
     */
    public $totAuthVehicles = null;

    /**
     * @Form\Type("Text")
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\AllowEmpty(true)
     * @Form\Required(true)
     * @Form\Attributes({"class":"short","id":"totAuthTrailers","required":false,"pattern":"\d*"})
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation.data.totAuthTrailers",
     *     "label_attributes": {"class": "form-element__question"},
     *     "short-label": "totAuthTrailers",
     *     "error-message": "operatingCentres_totAuthTrailers-error"
     * })
     * @Form\Validator({"name": "Digits"})
     * @Form\Validator({"name": "Between", "options": {"min":0, "max": 1000000}})
     * @Form\Filter({"name":"\Zend\Filter\Null", "options":{"type":"string"}})
     */
    public $totAuthTrailers = null;

    /**
     * @Form\Attributes({"class":"short","id":"totCommunityLicences"})
     * @Form\Options({"label": "application_operating-centres_authorisation.data.totCommunityLicences"})
     * @Form\Validator({"name": "Digits"})
     * @Form\Validator({"name": "Between", "options": {"min":0, "max": 1000000}})
     * @Form\Filter({"name":"\Zend\Filter\Null", "options": {"type":"string"} })
     */
    public $totCommunityLicences = null;
}
