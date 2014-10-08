<?php

/**
 * Type of licence fieldset
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Type("Zend\Form\Fieldset")
 */
class TypeOfLicence
{
    /**
    * @Form\Name("operator-location")
    * @Form\Options({
    *      "label": "application_type-of-licence_operator-location.data.niFlag",
    *      "value_options":{
    *          "N":"Great Britain",
    *          "Y":"Northern Ireland"
    *      }
    * })
    * @Form\Type("Radio")
    */
    public $operatorLocation = null;

    /**
    * @Form\Name("operator-type")
    * @Form\Options({
    *      "label": "application_type-of-licence_operator-type.data.goodsOrPsv",
    *      "value_options":{
    *          "lcat_gv":"Goods",
    *          "lcat_psv":"PSV"
    *      }
    * })
    * @Form\Type("Radio")
    */
    public $operatorType = null;

    /**
    * @Form\Name("licence-type")
    * @Form\Options({
    *      "label": "application_type-of-licence_licence-type.data.licenceType",
    *      "value_options":{
    *          "ltyp_r": "Restricted",
    *          "ltyp_sn": "Standard National",
    *          "ltyp_si": "Standard International",
    *          "ltyp_sr": "Special Restricted"
    *      }
    * })
    * @Form\Type("Radio")
    */
    public $licenceType = null;
}
