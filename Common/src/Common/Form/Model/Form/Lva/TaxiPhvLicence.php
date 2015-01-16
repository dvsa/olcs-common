<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("lva-taxi-phv-licence")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class TaxiPhvLicence
{
    /**
     * @Form\Name("data")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\TaxiPhvLicenceData")
     * @Form\Options({"label":"application_taxi-phv_licence-sub-action.data"})
     */
    public $data = null;

    /**
     * @Form\Name("contactDetails")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\TaxiPhvContactDetails")
     * @Form\Options({"label":"application_taxi-phv_licence-sub-action.contactDetails"})
     */
    public $contactDetails = null;

    /**
     * @Form\Name("address")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Address")
     */
    public $address = null;

    /**
     * @Form\Name("trafficArea")
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $trafficArea = null;

    /**
     * @Form\Name("form-actions")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormCrudActions")
     * @Form\Attributes({"class":"actions-container"})
     */
    public $formActions = null;
}
