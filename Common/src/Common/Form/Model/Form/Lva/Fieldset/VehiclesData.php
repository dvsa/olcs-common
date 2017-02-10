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
     *     "error-message": "vehiclesDate_hasEnteredReg-error",
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
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $hasEnteredReg = null;

    /**
     * @Form\Attributes({"value":"markup-application_vehicle-notice"})
     * @Form\Type("Common\Form\Elements\Types\HtmlTranslated")
     */
    public $notice = null;
}
