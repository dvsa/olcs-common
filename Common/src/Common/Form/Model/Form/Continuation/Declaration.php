<?php

namespace Common\Form\Model\Form\Continuation;

use Zend\Form\Annotation as Form;

/**
 * @Form\Attributes({"method":"post"})
 * @Form\Type("\Common\Form\Form")
 */
class Declaration
{
    /**
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\ComposedObject("\Common\Form\Model\Form\Continuation\Fieldset\DeclarationContent")
     */
    public $content = null;

    /**
     * @Form\ComposedObject("\Common\Form\Model\Form\Continuation\Fieldset\DeclarationSignatureDetails")
     */
    public $signatureDetails = null;

    /**
     * @Form\Name("form-actions")
     * @Form\ComposedObject("\Common\Form\Model\Form\Continuation\Fieldset\DeclarationFormActions")
     */
    public $formActions = null;
}
