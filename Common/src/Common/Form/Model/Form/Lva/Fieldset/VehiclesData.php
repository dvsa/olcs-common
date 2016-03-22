<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * Vehicle Data
 */
class VehiclesData
{
    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\Attributes({"id":"","placeholder":"","class":"js-enabled"})
     * @Form\Options({
     *     "fieldset-attributes": {
     *         "class": "checkbox inline"
     *     },
     *     "label": "application_vehicle-safety_vehicle-psv.hasEnteredReg",
     *     "value_options": {
     *         {
     *             "value": "Y",
     *             "label": "Yes",
     *             "label_attributes": {
     *                 "aria-label": "Are some of your vehicles or trailers inspected more often than this? No"
     *             }
     *         },
     *         {
     *             "value": "N",
     *             "label": "No"
     *         }
     *     },
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $hasEnteredReg = null;

    /**
     * @Form\Type("Common\Form\Elements\Types\Html")
     * @Form\Attributes({
     *      "value":
     *      "<p id=""notice"">If you don’t have vehicle details yet, you must
     *      tell the Central Licensing Office as soon as they’re available.</p>"
     * })
     * @Form\Options({"disable_html_escape": true})
     */
    public $notice = null;

    /**
     * @Form\Attributes({
     *  "value": "markup-about-vehicle-authorisation"
     * })
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $aboutAuthorisation = null;
}
