<?php

namespace Common\Form\Model\Form\Continuation;

use Zend\Form\Annotation as Form;

/**
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
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
     * @Form\Attributes({"type":"submit","class":"action--primary large"})
     * @Form\Options({"label":"Continue"})
     * @Form\Type("\Zend\Form\Element\Button")
     */
    public $submit = null;
}