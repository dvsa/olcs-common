<?php

namespace Common\Form\Model\Form\Continuation\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("viewVehiclesSection")
 */
class ViewVehiclesSection
{
    /**
     * @Form\Attributes({"value": "markup-continuation-licence-checklist-vehicles-header"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $vehiclesHeader = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--secondary large", "id": "viewPeople", "target":"_blank"})
     * @Form\Options({"label": "continuations.vehicles.button-label"})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionLink")
     */
    public $viewVehicles = null;

    /**
     * @Form\Attributes({"value": "markup-continuation-licence-checklist-help-message"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $message = null;
}
