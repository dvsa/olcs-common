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
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation.dataTrafficArea.label.new",
     *     "value_options": {
     *
     *     },
     *     "empty_option": "Please Select",
     *     "disable_inarray_validator": false,
     *     "help-block": "Please select a category",
     *     "hint": "application_operating-centres_authorisation.dataTrafficArea.hint.new"
     * })
     * @Form\Type("\Zend\Form\Element\Select")
     */
    public $trafficArea = null;

    /**
     * @Form\Attributes({
     *     "value": "application_operating-centres_authorisation.dataTrafficArea.label.exists"
     * })
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $trafficAreaInfoLabelExists = null;

    /**
     * @Form\Attributes({"value":"<h3>%NAME%</h3>"})
     * @Form\Type("\Common\Form\Elements\Types\Html")
     */
    public $trafficAreaInfoNameExists = null;

    /**
     * @Form\Attributes({
     *     "value": "application_operating-centres_authorisation.dataTrafficArea.labelasahint.exists"
     * })
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $trafficAreaInfoHintExists = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $hiddenId = null;
}
