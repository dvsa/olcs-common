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
    public $licenceType = null;

    /**
     * @Form\Attributes({"class":"short","id":""})
     * @Form\Input("\Common\InputFilter\ContinueIfEmptyInput")
     * @Form\AllowEmpty(false)
     * @Form\Options({"label": "application_operating-centres_authorisation.data.totAuthSmallVehicles"})
     * @Form\Validator({"name": "Digits"})
     * @Form\Validator({"name": "Between", "options": {"min":0, "max": 1000000}})
     * @Form\Filter({"name":"\Zend\Filter\Null", "options": {"type":"string"} })
     */
    public $totAuthSmallVehicles = null;

    /**
     * @Form\Attributes({"class":"short","id":""})
     * @Form\Input("\Common\InputFilter\ContinueIfEmptyInput")
     * @Form\AllowEmpty(false)
     * @Form\Options({"label": "application_operating-centres_authorisation.data.totAuthMediumVehicles"})
     * @Form\Validator({"name": "Digits"})
     * @Form\Validator({"name": "Between", "options": {"min":0, "max": 1000000}})
     * @Form\Filter({"name":"\Zend\Filter\Null", "options": {"type":"string"} })
     */
    public $totAuthMediumVehicles = null;

    /**
     * @Form\Attributes({"class":"short","id":""})
     * @Form\Input("\Common\InputFilter\ContinueIfEmptyInput")
     * @Form\AllowEmpty(false)
     * @Form\Options({"label": "application_operating-centres_authorisation.data.totAuthLargeVehicles"})
     * @Form\Validator({"name": "Digits"})
     * @Form\Validator({"name": "Between", "options": {"min":0, "max": 1000000}})
     * @Form\Filter({"name":"\Zend\Filter\Null", "options": {"type":"string"} })
     */
    public $totAuthLargeVehicles = null;

    /**
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\AllowEmpty(true)
     * @Form\Required(true)
     * @Form\Attributes({"class":"short","id":"","required":false})
     * @Form\Options({"label": "application_operating-centres_authorisation.data.totAuthVehicles"})
     * @Form\Validator({"name": "Common\Form\Elements\Validators\OperatingCentreTotalVehicleAuthorisationsValidator"})
     * @Form\Validator({
     *     "name": "Common\Form\Elements\Validators\EqualSum",
     *     "options": {
     *         "errorPrefix": "lva-operating-centre-tot-auth-vehicles-equalsum",
     *         "fields":{"totAuthSmallVehicles", "totAuthMediumVehicles", "totAuthLargeVehicles"}
     *     }
     * })
     * @Form\Filter({"name":"\Zend\Filter\Null", "options": {"type":"string"} })
     */
    public $totAuthVehicles = null;

    /**
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\AllowEmpty(true)
     * @Form\Required(true)
     * @Form\Attributes({"class":"short","id":"","required":false})
     * @Form\Options({"label": "application_operating-centres_authorisation.data.totAuthTrailers"})
     * @Form\Validator({"name": "Common\Form\Elements\Validators\OperatingCentreTrailerAuthorisationsValidator"});
     * @Form\Filter({"name":"\Zend\Filter\Null", "options": {"type":"string"} })
     */
    public $totAuthTrailers = null;

    /**
     * @Form\Input("\Common\InputFilter\ContinueIfEmptyInput")
     * @Form\AllowEmpty(false)
     * @Form\Attributes({"class":"short","id":""})
     * @Form\Options({"label": "application_operating-centres_authorisation.data.totCommunityLicences"})
     * @Form\Validator({"name": "Digits"})
     * @Form\Validator({"name": "Between", "options": {"min":0, "max": 1000000}})
     * @Form\Validator({"name": "Common\Form\Elements\Validators\OperatingCentreCommunityLicencesValidator"})
     * @Form\Filter({"name":"\Zend\Filter\Null", "options": {"type":"string"} })
     */
    public $totCommunityLicences = null;

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
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $noOfOperatingCentres = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $minVehicleAuth = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $maxVehicleAuth = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $minTrailerAuth = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $maxTrailerAuth = null;
}
