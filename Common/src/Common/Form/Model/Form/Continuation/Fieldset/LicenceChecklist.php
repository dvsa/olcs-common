<?php

namespace Common\Form\Model\Form\Continuation\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("data")
 */
class LicenceChecklist
{
    /**
     * @Form\Type("Common\Form\Elements\Types\CheckboxAdvanced")
     * @Form\Required(true)
     * @Form\Attributes({
     *      "id":"typeOfLicenceCheckbox",
     *      "data-js-validate":"required",
     *      "data-show-element":"#typeOfLicenceCheckbox-hidden",
     *      "class":"checkbox"
     * })
     * @Form\Options({
     *      "label":"continuations.type-of-licence-checkbox.label",
     *      "label_attributes": {"class": "form-control form-control--checkbox form-control--confirm"},
     *      "content":"partials/continuation/licence-checklist-type-of-licence",
     *      "checked_value":"Y",
     *      "unchecked_value":"N",
     *      "must_be_value": "Y",
     *      "not_checked_message":"continuations.checklist.section.error",
     * })
     */
    public $typeOfLicenceCheckbox = null;

    /**
     * @Form\Type("Common\Form\Elements\Types\CheckboxAdvanced")
     * @Form\Required(true)
     * @Form\Attributes({
     *      "id":"businessTypeCheckbox",
     *      "data-js-validate":"required",
     *      "data-show-element":"#businessTypeCheckbox-hidden",
     *      "class":"checkbox"
     * })
     * @Form\Options({
     *     "label":"continuations.business-type-checkbox.label",
     *     "label_attributes": {"class": "form-control form-control--checkbox form-control--confirm"},
     *     "content":"partials/continuation/licence-checklist-business-type",
     *     "checked_value":"Y",
     *     "unchecked_value":"N",
     *     "must_be_value": "Y",
     *     "not_checked_message":"continuations.checklist.section.error"
     * })
     */
    public $businessTypeCheckbox = null;

    /**
     * @Form\Type("Common\Form\Elements\Types\CheckboxAdvanced")
     * @Form\Required(true)
     * @Form\Attributes({
     *      "id":"businessDetailsCheckbox",
     *      "data-js-validate":"required",
     *      "data-show-element":"#businessDetailsCheckbox-hidden",
     *      "class":"checkbox"
     * })
     * @Form\Options({
     *     "label":"continuations.business-details-checkbox.label",
     *     "label_attributes": {"class": "form-control form-control--checkbox form-control--confirm"},
     *     "content":"partials/continuation/licence-checklist-business-details",
     *     "checked_value":"Y",
     *     "unchecked_value":"N",
     *     "must_be_value": "Y",
     *     "not_checked_message":"continuations.checklist.section.error"
     * })
     */
    public $businessDetailsCheckbox = null;

    /**
     * @Form\Type("Common\Form\Elements\Types\CheckboxAdvanced")
     * @Form\Required(true)
     * @Form\Attributes({
     *      "id":"peopleCheckbox",
     *      "data-js-validate":"required",
     *      "data-show-element":"#peopleCheckbox-hidden",
     *      "class":"checkbox"
     * })
     * @Form\Options({
     *     "label":"continuations.people-checkbox.label.",
     *     "label_attributes": {"class": "form-control form-control--checkbox form-control--confirm"},
     *     "content":"partials/continuation/licence-checklist-people",
     *     "checked_value":"Y",
     *     "unchecked_value":"N",
     *     "must_be_value": "Y",
     *     "not_checked_message":"continuations.checklist.section.error"
     * })
     */
    public $peopleCheckbox = null;

    /**
     * @Form\Type("Common\Form\Elements\Types\CheckboxAdvanced")
     * @Form\Required(true)
     * @Form\Attributes({
     *      "id":"vehiclesCheckbox",
     *      "data-js-validate":"required",
     *      "data-show-element":"#vehiclesCheckbox-hidden",
     *      "class":"checkbox"
     * })
     * @Form\Options({
     *     "label":"continuations.vehicles-checkbox.label",
     *     "label_attributes": {"class": "form-control form-control--checkbox form-control--confirm"},
     *     "content":"partials/continuation/licence-checklist-vehicles",
     *     "checked_value":"Y",
     *     "unchecked_value":"N",
     *     "must_be_value": "Y",
     *     "not_checked_message":"continuations.checklist.section.error"
     * })
     */
    public $vehiclesCheckbox = null;

    /**
     * @Form\ComposedObject("Common\Form\Model\Form\Continuation\Fieldset\LicenceChecklistConfirmation")
     * @Form\Options(
     *     {
     *          "label" : "continuations.checklist.confirmation.label",
     *          "hint":"continuations.checklist.confirmation.hint",
     *     }
     * )
     */
    public $licenceChecklistConfirmation = null;
}
