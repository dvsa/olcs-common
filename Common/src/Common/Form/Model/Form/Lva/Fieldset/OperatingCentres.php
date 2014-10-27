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
     * @Form\Options({
     *     "label":
     * "application_operating-centres_authorisation.data.totAuthSmallVehicles"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\OperatingCentreVehicleAuthorisations")
     */
    public $totAuthSmallVehicles = null;

    /**
     * @Form\Attributes({"class":"short","id":""})
     * @Form\Options({
     *     "label":
     * "application_operating-centres_authorisation.data.totAuthMediumVehicles"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\OperatingCentreVehicleAuthorisations")
     */
    public $totAuthMediumVehicles = null;

    /**
     * @Form\Attributes({"class":"short","id":""})
     * @Form\Options({
     *     "label":
     * "application_operating-centres_authorisation.data.totAuthLargeVehicles"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\OperatingCentreVehicleAuthorisations")
     */
    public $totAuthLargeVehicles = null;

    /**
     * @Form\Attributes({"class":"short","id":""})
     * @Form\Options({
     *     "label":
     * "application_operating-centres_authorisation.data.totCommunityLicences"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\OperatingCentreCommunityLicences")
     */
    public $totCommunityLicences = null;

    /**
     * @Form\Attributes({"class":"short","id":""})
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation.data.totAuthVehicles"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\OperatingCentreTotalVehicleAuthorisations")
     */
    public $totAuthVehicles = null;

    /**
     * @Form\Attributes({"class":"short","id":""})
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation.data.totAuthTrailers"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\OperatingCentreTrailerAuthorisations")
     */
    public $totAuthTrailers = null;

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
