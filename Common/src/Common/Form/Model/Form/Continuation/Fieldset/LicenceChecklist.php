<?php

namespace Common\Form\Model\Form\Continuation\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("data")
 */
class LicenceChecklist
{
    /**
     * @Form\Type("OlcsCheckbox")
     * @Form\Attributes({"id":"typeOfLicenceCheckbox"})
     * @Form\Options({
     *     "label":"continuations.type-of-licence-checkbox.label",
     *     "label_attributes": {"class": "form-control form-control--checkbox form-control--confirm"},
     *     "checked_value":"Y",
     *     "unchecked_value":"N",
     * })
     */
    public $typeOfLicenceCheckbox = null;

    /**
     * @Form\Attributes({"value": "markup-continuation-licence-checklist-type-of-licence"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $typeOfLicence = null;

    /**
     * @Form\Type("OlcsCheckbox")
     * @Form\Attributes({"id":"businessTypeCheckbox"})
     * @Form\Options({
     *     "label":"continuations.business-type-checkbox.label",
     *     "label_attributes": {"class": "form-control form-control--checkbox form-control--confirm"},
     *     "checked_value":"Y",
     *     "unchecked_value":"N",
     * })
     */
    public $businessTypeCheckbox = null;

    /**
     * @Form\Attributes({"value": "markup-continuation-licence-checklist-business-type"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $businessType = null;

    /**
     * @Form\Type("OlcsCheckbox")
     * @Form\Attributes({"id":"businessTypeCheckbox"})
     * @Form\Options({
     *     "label":"continuations.business-details-checkbox.label",
     *     "label_attributes": {"class": "form-control form-control--checkbox form-control--confirm"},
     *     "checked_value":"Y",
     *     "unchecked_value":"N",
     * })
     */
    public $businessDetailsCheckbox = null;

    /**
     * @Form\Attributes({"value": "markup-continuation-licence-checklist-business-details"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $businessDetails = null;

    /**
     * @Form\Type("OlcsCheckbox")
     * @Form\Attributes({"id":"peopleCheckbox"})
     * @Form\Options({
     *     "label":"continuations.people-checkbox.label.",
     *     "label_attributes": {"class": "form-control form-control--checkbox form-control--confirm"},
     *     "checked_value":"Y",
     *     "unchecked_value":"N",
     * })
     */
    public $peopleCheckbox = null;

    /**
     * @Form\Name("viewPeopleSection")
     * @Form\ComposedObject("Common\Form\Model\Form\Continuation\Fieldset\ViewPeopleSection")
     */
    public $viewPeopleSection = null;

    /**
     * @Form\Attributes({"value": "markup-continuation-licence-checklist-people"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $people = null;

    /**
     * @Form\Type("OlcsCheckbox")
     * @Form\Attributes({"id":"vehiclesCheckbox"})
     * @Form\Options({
     *     "label":"continuations.vehicles-checkbox.label",
     *     "label_attributes": {"class": "form-control form-control--checkbox form-control--confirm"},
     *     "checked_value":"Y",
     *     "unchecked_value":"N",
     * })
     */
    public $vehiclesCheckbox = null;

    /**
     * @Form\Name("viewVehiclesSection")
     * @Form\ComposedObject("Common\Form\Model\Form\Continuation\Fieldset\ViewVehiclesSection")
     */
    public $viewVehiclesSection = null;

    /**
     * @Form\Attributes({"value": "markup-continuation-licence-checklist-vehicles"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $vehicles = null;
}
