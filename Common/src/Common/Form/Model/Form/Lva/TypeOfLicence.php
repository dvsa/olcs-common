<?php

/**
 * Type of licence form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("lva-type-of-licence")
 * @Form\Options({"label":"Type of licence"})
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 * @Form\Options({"prefer_form_input_filter": true})
 */
class TypeOfLicence
{
    /**
     * @Form\Name("operator-location")
     * @Form\Options({"label":"Operator location"})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\OperatorLocation")
     */
    public $operatorLocation = null;

    /**
     * @Form\Name("operator-type")
     * @Form\Options({"label":"Operator type"})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\OperatorType")
     */
    public $operatorType = null;

    /**
     * @Form\Name("licence-type")
     * @Form\Options({"label":"Licence type"})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\LicenceType")
     */
    public $licenceType = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Form\Fieldset\FormActions")
     */
    public $formActions = null;
}
