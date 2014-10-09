<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("application_taxi-phv_licence")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class ApplicationTaxiPhvLicence
{

    /**
     * @Form\Name("table")
     * @Form\Options({})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\ApplicationTaxiPhvLicenceTable")
     */
    public $table = null;

    /**
     * @Form\Name("dataTrafficArea")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\ApplicationTaxiPhvLicenceDataTrafficArea")
     */
    public $dataTrafficArea = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\JourneyButtons")
     */
    public $formActions = null;


}

