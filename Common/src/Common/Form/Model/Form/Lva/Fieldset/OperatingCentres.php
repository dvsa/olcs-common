<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Laminas\Form\Annotation as Form;

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
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\OperatingCentresTotAuthHgvVehicles")
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation.data.totAuthHgvVehiclesFieldset.hgvs-label",
     *     "hint": "markup-operating-centres-authorisation",
     *     "hint-position": "below"
     * })
     */
    public $totAuthHgvVehiclesFieldset = null;

    /**
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\OperatingCentresTotAuthLgvVehicles")
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation.data.totAuthLgvVehiclesFieldset.label"
     * })
     */
    public $totAuthLgvVehiclesFieldset = null;

    /**
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\OperatingCentresTotAuthTrailers")
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation.data.totAuthTrailersFieldset.label"
     * })
     */
    public $totAuthTrailersFieldset = null;

    /**
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\OperatingCentresTotCommunityLicences")
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation.data.totCommunityLicencesFieldset.label"
     * })
     */
    public $totCommunityLicencesFieldset = null;
}
