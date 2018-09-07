<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @Form\Attributes({"method":"post", "class":"form"})
 * @Form\Type("\Common\Form\Form")
 */
class TransportManagerApplicationDeclaration
{
    /**
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\TransportManager\ApplicationDeclaration")
     */
    public $content = null;

    /**
     * @Form\Name("form-actions")
     * @Form\ComposedObject("\Common\Form\Model\Fieldset\SubmitButton")
     */
    public $formActions = null;
}