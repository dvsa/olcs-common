<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("dataTrafficArea")
 * @Form\Attributes({
 *      "class": "traffic-area"
 * })
 */
class TrafficArea
{
    /**
     * @Form\Attributes({"id":"trafficArea","placeholder":""})
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation.dataTrafficArea.label.new",
     *     "hint" : "markup-traffic-area-help",
     *     "value_options": {
     *
     *     },
     *     "empty_option": "Please Select",
     *     "disable_inarray_validator": false,
     * })
     * @Form\Type("\Zend\Form\Element\Select")
     */
    public $trafficArea = null;

    /**
     *
     * @Form\Type("Common\Form\Elements\Types\TrafficAreaSet")
     * @Form\Options({
     *     "hint" : "markup-traffic-area-help",
     * })
     */
    public $trafficAreaSet = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Required(false)
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation.enforcementArea.label",
     *     "value_options": {
     *
     *     },
     *     "empty_option": "Please Select",
     *     "disable_inarray_validator": false,
     * })
     * @Form\Type("\Zend\Form\Element\Select")
     */
    public $enforcementArea = null;
}
