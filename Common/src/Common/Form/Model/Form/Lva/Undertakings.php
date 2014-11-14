<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("lva-undertakings")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class Undertakings
{
    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormActions")
     */
    public $formActions = null;
}
