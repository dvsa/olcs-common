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
     */
    public $totAuthSmallVehicles = null;

    /**
     * @Form\Attributes({"class":"short","id":""})
     * @Form\Input("\Common\InputFilter\ContinueIfEmptyInput")
     * @Form\AllowEmpty(false)
     * @Form\Options({"label": "application_operating-centres_authorisation.data.totAuthMediumVehicles"})
     * @Form\Validator({"name": "Digits"})
     * @Form\Validator({"name": "Between", "options": {"min":0, "max": 1000000}})
     */
    public $totAuthMediumVehicles = null;

    /**
     * @Form\Attributes({"class":"short","id":""})
     * @Form\Input("\Common\InputFilter\ContinueIfEmptyInput")
     * @Form\AllowEmpty(false)
     * @Form\Options({"label": "application_operating-centres_authorisation.data.totAuthLargeVehicles"})
     * @Form\Validator({"name": "Digits"})
     * @Form\Validator({"name": "Between", "options": {"min":0, "max": 1000000}})
     */
    public $totAuthLargeVehicles = null;

    /**
     * @Form\Attributes({"class":"short","id":""})
     * @Form\Input("\Common\InputFilter\ContinueIfEmptyInput")
     * @Form\AllowEmpty(false)
     * @Form\Options({"label": "application_operating-centres_authorisation.data.totAuthVehicles"})
     * @Form\Validator({"name": "Digits"})
     * @Form\Validator({"name": "Between", "options": {"min":0, "max": 1000000}})
     * @Form\Validator({"name": "Common\Form\Elements\Validators\OperatingCentreTotalVehicleAuthorisationsValidator"})
     * @Form\Validator({
     *     "name": "Common\Form\Elements\Validators\EqualSum",
     *     "options": {
     *         "errorPrefix": "lva-operating-centre-tot-auth-vehicles-equalsum",
     *         "fields":{"totAuthSmallVehicles", "totAuthMediumVehicles", "totAuthLargeVehicles"}
     *     }
     * })
     */
    public $totAuthVehicles = null;

    /**
     * @Form\Attributes({"class":"short","id":""})
     * @Form\Input("\Common\InputFilter\ContinueIfEmptyInput")
     * @Form\AllowEmpty(false)
     * @Form\Options({"label": "application_operating-centres_authorisation.data.totAuthTrailers"})
     * @Form\Validator({"name": "Digits"})
     * @Form\Validator({"name": "Between", "options": {"min":0, "max": 1000000}})
     * @Form\Validator({"name": "Common\Form\Elements\Validators\OperatingCentreTrailerAuthorisationsValidator"});
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
