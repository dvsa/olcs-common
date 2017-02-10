<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("lva-safety-application")
 */
class SafetyApplication
{
    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label":
     * "application_vehicle-safety_safety.application.suitableMaintenance",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $isMaintenanceSuitable = null;

    /**
     * @Form\Attributes({
     *     "id":"","placeholder":"",
     *     "data-container-class":"confirm"
     * })
     * @Form\Options({
     *     "checked_value": "Y",
     *     "unchecked_value": "N",
     *     "label": "application_vehicle-safety_safety.application.safetyConfirmation",
     *     "must_be_value": "Y"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     */
    public $safetyConfirmation = null;
}
