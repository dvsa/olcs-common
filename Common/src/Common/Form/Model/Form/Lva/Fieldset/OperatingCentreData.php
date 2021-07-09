<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Laminas\Form\Annotation as Form;

/**
 * Operating centre fieldset
 */
class OperatingCentreData
{
    /**
     * @Form\Attributes({"value":"<h2>%s</h2>"})
     * @Form\Options({"tokens":{"application_operating-centres_authorisation-sub-action.data"}})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $dataHtml = null;

    /**
     * @Form\Attributes({"value":"<h3>%s</h3>"})
     * @Form\Options({"tokens":{"application_operating-centres_authorisation-sub-action.data.hgv-html"}})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $hgvHtml = null;

    /**
     * @Form\Attributes({"class":"tiny","pattern":"\d*","id":"noOfHgvVehiclesRequired"})
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation-sub-action.data.noOfHgvVehiclesRequired",
     *     "hint": "application_operating-centres_authorisation-sub-action.data.noOfHgvVehiclesRequired.hint",
     *     "error-message": "application_operating-centres_authorisation-sub-action.data.noOfHgvVehiclesRequired.error-message"
     * })
     * @Form\Validator({"name": "Between", "options": {"min":0, "max":1000000}})
     */
    public $noOfHgvVehiclesRequired = null;

    /**
     * @Form\Attributes({"value":"<h3>%s</h3>"})
     * @Form\Options({"tokens":{"application_operating-centres_authorisation-sub-action.data.lgv-html"}})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $lgvHtml = null;

    /**
     * @Form\Attributes({"class":"tiny","pattern":"\d*","id":"noOfLgvVehiclesRequired"})
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation-sub-action.data.noOfLgvVehiclesRequired",
     *     "hint": "application_operating-centres_authorisation-sub-action.data.noOfLgvVehiclesRequired.hint",
     *     "error-message": "application_operating-centres_authorisation-sub-action.data.noOfLgvVehiclesRequired.error-message"
     * })
     * @Form\Validator({"name": "Between", "options": {"min":0, "max":1000000}})
     */
    public $noOfLgvVehiclesRequired = null;

    /**
     * @Form\Attributes({"value":"<h3>%s</h3>"})
     * @Form\Options({"tokens":{"application_operating-centres_authorisation-sub-action.data.trailers-html"}})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $trailersHtml = null;

    /**
     * @Form\Attributes({"class":"tiny","pattern":"\d*","id":"noOfTrailersRequired"})
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation-sub-action.data.noOfTrailersRequired",
     *     "error-message": "Your total number of trailers"
     * })
     * @Form\Validator({"name": "Between", "options": {"min":0, "max":1000000}})
     */
    public $noOfTrailersRequired = null;

    /**
     * @Form\Name("permission")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\OperatingCentrePermission")
     * @Form\Options({"label":"lva-operating-centre-newspaper-permission"})
     */
    public $permission = null;

    /**
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $guidance = null;
}
