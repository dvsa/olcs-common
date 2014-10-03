<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("application_type-of-licence_licence-type")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class ApplicationTypeOfLicenceLicenceType
{

    /**
     * @Form\Name("data")
     * @Form\Options({"label":"application_type-of-licence_licence-type.data"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\ApplicationTypeOfLicenceLicenceTypeData")
     */
    public $data = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\JourneyButtons")
     */
    public $formActions = null;


}

