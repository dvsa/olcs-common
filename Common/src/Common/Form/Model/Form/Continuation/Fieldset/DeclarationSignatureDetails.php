<?php

namespace Common\Form\Model\Form\Continuation\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * Class DeclarationSignatureDetails
 */
class DeclarationSignatureDetails
{
    /**
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     * @Form\Attributes({"data-container-class": "verify"})
     */
    public $signature = null;
}
