<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("lva-undertakings")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class ApplicationUndertakings
{
    /**
     * @Form\Name("declarationsAndUndertakings")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\ApplicationDeclarationsAndUndertakings")
     */
    public $declarationsAndUndertakings = null;

    /**
     * @Form\Name("form-actions")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormActions")
     * @Form\Attributes({"class":"actions-container"})
     */
    public $formActions = null;
}
