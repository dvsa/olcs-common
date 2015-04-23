<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Type("Zend\Form\Fieldset")
 */
class GenerateContinuationDetails
{
    /**
     * @Form\Attributes({"id":"generate-continuation-type","placeholder":""})
     * @Form\Options({
     *     "label": "Type",
     *     "value_options": {
     *         "operator": "Operator licences",
     *         "irfo": "IRFO licences"
     *     }
     * })
     * @Form\Type("\Zend\Form\Element\Select")
     */
    public $type = null;

    /**
     * @Form\Attributes({"id":"generate-continuation-date","placeholder":""})
     * @Form\Options({
     *     "label": "Date",
     *     "min_year_delta": "-5",
     *     "max_year_delta": "+5",
     *     "default_date": "now"
     * })
     * @Form\Type("MonthSelect")
     */
    public $date = null;

    /**
     * @Form\Attributes({"id":"generate-continuation-trafficArea","placeholder":""})
     * @Form\Options({
     *     "label": "Traffic area",
     *     "disable_inarray_validator": false,
     *     "service_name": "Entity\TrafficArea"
     * })
     * @Form\Type("DynamicSelect")
     */
    public $trafficArea = null;
}
