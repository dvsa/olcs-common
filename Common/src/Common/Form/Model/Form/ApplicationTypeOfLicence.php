<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("application_type-of-licence")
 * @Form\Attributes({"method":"post","class":"js-submit"})
 * @Form\Type("Common\Form\Form")
 */
class ApplicationTypeOfLicence
{

    /**
     * @Form\Name("operator-location")
     * @Form\Attributes({"class":"hidden"})
     * @Form\Options({"label":"application_type-of-licence_operator-location.data"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\OperatorLocation")
     */
    public $operatorLocation = null;

    /**
     * @Form\Name("operator-type")
     * @Form\Attributes({"class":"hidden"})
     * @Form\Options({"label":"application_type-of-licence_operator-type.data"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\OperatorType")
     */
    public $operatorType = null;

    /**
     * @Form\Name("licence-type")
     * @Form\Attributes({"class":"hidden"})
     * @Form\Options({"label":"application_type-of-licence_licence-type.data"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\LicenceType")
     */
    public $licenceType = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\JourneyButtons")
     */
    public $formActions = null;


}

