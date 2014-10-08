<?php

/**
 * Type of licence form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;
use Common\Form\Model\Form\Traits\VersionTrait;

/**
 * @Form\Name("lva-type-of-licence")
 * @Form\Options({"label":"Type of licence"})
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 * @Form\Options({"prefer_form_input_filter": true})
 */
class TypeOfLicence
{
    use VersionTrait;

    /**
     * @Form\Name("type-of-licence")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\TypeOfLicence")
     */
    public $typeOfLicence = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Form\Fieldset\FormActions")
     */
    public $formActions = null;
}
