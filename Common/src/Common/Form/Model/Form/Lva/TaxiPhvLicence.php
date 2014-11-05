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
     * @Form\Options({"label":"application_taxi-phv_licence-sub-action.data"})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\TaxiPhvLicenceData")
     */
    public $data = null;

    /**
     * @Form\Name("contactDetails")
     * @Form\Options({"label":"application_taxi-phv_licence-sub-action.contactDetails"})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\TaxiPhvContactDetails")
     */
    public $contactDetails = null;

    /**
     * @Form\Name("address")
     * @Form\Options({})
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
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormCrudActions")
     */
    public $formActions = null;
}
