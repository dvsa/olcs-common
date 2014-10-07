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
}
