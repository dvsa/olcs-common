<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"label":""})
 */
class VehiclesTransferDetails
{
    /**
     * @Form\Required(true)
     * @Form\Attributes({"id":"","placeholder":"","required":false})
     * @Form\Options({
     *     "label": "licence.vehicles_transfer.form.licence",
     *     "empty_option": "Please select"
     * })
     * @Form\Type("Select")
     * @Form\Validator({
     *      "name": "Zend\Validator\NotEmpty",
     *      "options": {
     *          "messages":{Zend\Validator\NotEmpty::IS_EMPTY:"licence.vehicles_transfer.form.message_empty"}
     *      }
     * })
     */
    public $licence = null;
}
