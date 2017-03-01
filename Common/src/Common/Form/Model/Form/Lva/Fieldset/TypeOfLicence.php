<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Type("Zend\Form\Fieldset")
 */
class TypeOfLicence
{
    /**
     * @Form\Name("operator-location")
     * @Form\Attributes({"id": ""})
     * @Form\Options({
     *      "short-label": "short-label-tol-operator-location",
     *      "fieldset-attributes": {"id": "operator-location"},
     *      "fieldset-data-group": "operator-location",
     *      "label": "application_type-of-licence_operator-location.data.niFlag",
     *      "label_attributes": {"class": "form-control form-control--radio"},
     *      "value_options": {
     *          {"value": "N", "label": "Great Britain"},
     *          {"value": "Y", "label": "Northern Ireland"}
     *      }
     * })
     * @Form\Type("Radio")
     */
    public $operatorLocation = null;

    /**
     * @Form\Name("operator-type")
     * @Form\Attributes({"id": ""})
     * @Form\Options({
     *      "short-label": "short-label-tol-operator-type",
     *      "error-message": "operator-type-error",
     *      "fieldset-attributes": {"id": "operator-type"},
     *      "fieldset-data-group": "operator-type",
     *      "label": "application_type-of-licence_operator-type.data.goodsOrPsv",
     *      "label_attributes": {"class": "form-control form-control--radio"},
     *      "value_options": {
     *          {"value": "lcat_gv", "label": "Goods vehicles"},
     *          "lcat_psv": "Public service vehicles"
     *      }
     * })
     * @Form\Type("Radio")
     */
    public $operatorType = null;

    /**
     * @Form\Name("licence-type")
     * @Form\Attributes({"id": ""})
     * @Form\Options({
     *      "short-label": "short-label-tol-licence-type",
     *      "error-message": "type-of-licence-error",
     *      "fieldset-attributes": {"id": "licence-type"},
     *      "fieldset-data-group": "licence-type",
     *      "label": "application_type-of-licence_licence-type.data.licenceType",
     *      "label_attributes": {"class": "form-control form-control--radio"},
     *      "hint": "markup-typeOfLicence_licenceType-hint",
     *      "value_options": {
     *          {"value": "ltyp_r", "label": "Restricted"},
     *          "ltyp_sn": "Standard National",
     *          "ltyp_si": "Standard International",
     *          "ltyp_sr": "Special Restricted"
     *      }
     * })
     * @Form\Type("Radio")
     */
    public $licenceType = null;
}
