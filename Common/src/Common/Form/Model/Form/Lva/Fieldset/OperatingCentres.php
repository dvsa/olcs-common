<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({
 *     "label": "application_operating-centres_authorisation.data",
 *     "hint": "application_operating-centres_authorisation.data.hint"
 * })
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
     * @Form\Attributes({"class":"short","id":"","required":false})
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation.data.totAuthVehicles",
     *     "short-label": "totAuthVehicles"
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
     * @Form\Attributes({"class":"short","id":"","required":false})
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation.data.totAuthTrailers",
     *     "short-label": "totAuthTrailers"
     * })
     * @Form\Validator({"name": "Digits"})
     * @Form\Validator({"name": "Between", "options": {"min":0, "max": 1000000}})
     * @Form\Filter({"name":"\Zend\Filter\Null", "options":{"type":"string"}})
     */
    public $totAuthTrailers = null;

    /**
     * @Form\Attributes({"class":"short","id":""})
     * @Form\Options({"label": "application_operating-centres_authorisation.data.totCommunityLicences"})
     * @Form\Validator({"name": "Digits"})
     * @Form\Validator({"name": "Between", "options": {"min":0, "max": 1000000}})
     * @Form\Filter({"name":"\Zend\Filter\Null", "options": {"type":"string"} })
     */
    public $totCommunityLicences = null;
}
