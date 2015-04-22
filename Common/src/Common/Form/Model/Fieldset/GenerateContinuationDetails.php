<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Type("Zend\Form\Fieldset")
 */
class GenerateContinuationDetails
{
    /**
     * @Form\Attributes({"id":"adPlaced","placeholder":""})
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
     * @Form\Attributes({})
     * @Form\Options({
     *     "label": "Date"
     * })
     * @Form\Type("MonthSelect")
     */
    public $month = null;
}
