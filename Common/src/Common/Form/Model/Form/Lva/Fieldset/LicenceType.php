<?php

/**
 * Licence type fieldset
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("licence-type")
 */
class LicenceType
{
    /**
    * @Form\Name("licenceType")
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
