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
     *      "fieldset-attributes": {
     *          "id": "operator-location",
     *          "class": "checkbox"
     *      },
     *      "fieldset-data-group": "operator-location",
     *      "label": "application_type-of-licence_operator-location.data.niFlag",
     *      "value_options": {
     *          {
     *              "value": "N",
     *              "label": "Great Britain",
     *              "label_attributes": {
     *                  "aria-label": "Where will you operate? Great Britain"
     *              }
     *          },
     *          {
     *              "value": "Y",
     *              "label": "Northern Ireland"
     *          }
     *      }
     * })
     * @Form\Type("Radio")
     */
    public $operatorLocation = null;

    /**
     * @Form\Name("operator-type")
     * @Form\Attributes({"id": ""})
     * @Form\Required(false)
     * @Form\Options({
     *      "short-label": "short-label-tol-operator-type",
     *      "fieldset-attributes": {
     *          "id": "operator-type",
     *          "class": "checkbox"
     *      },
     *      "fieldset-data-group": "operator-type",
     *      "label": "application_type-of-licence_operator-type.data.goodsOrPsv",
     *      "value_options": {
     *          {
     *              "value": "lcat_gv",
     *              "label": "Goods vehicles",
     *              "label_attributes": {
     *                  "aria-label": "What types of vehicles will you be operating, goods vehicles"
     *              }
     *          },
     *          "lcat_psv": "Public service vehicles"
     *      }
     * })
     * @Form\Type("Radio")
     */
    public $operatorType = null;

    /**
     * @Form\Attributes({
     *  "value": "markup-type-of-licence-difference",
     *  "data-container-class":"js-difference-guidance"
     * })
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $difference = null;

    /**
     * @Form\Name("licence-type")
     * @Form\Attributes({"id": ""})
     * @Form\Options({
     *      "short-label": "short-label-tol-licence-type",
     *      "fieldset-attributes": {
     *          "id": "licence-type",
     *          "class": "checkbox"
     *      },
     *      "fieldset-data-group": "licence-type",
     *      "label": "application_type-of-licence_licence-type.data.licenceType",
     *      "hint": "application_type-of-licence_licence-type.data.licenceType-hint",
     *      "value_options": {
     *          {
     *              "value": "ltyp_r",
     *              "label": "Restricted",
     *              "label_attributes": {
     *                  "aria-label": "What type of licence do you want to apply for? Restricted"
     *              }
     *          },
     *          "ltyp_sn": "Standard National",
     *          "ltyp_si": "Standard International",
     *          "ltyp_sr": "Special Restricted"
     *      }
     * })
     * @Form\Type("Radio")
     */
    public $licenceType = null;
}
