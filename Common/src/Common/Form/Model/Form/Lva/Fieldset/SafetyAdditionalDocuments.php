<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("additional-documents")
 */
class SafetyAdditionalDocuments
{
    /**
     * @Form\Attributes({"value": "markup-safety-additional-documents"})
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $additionalInfo = null;

    /**
     * @Form\ComposedObject("\Common\Form\Model\Fieldset\MultipleFileUpload")
     * @Form\Attributes({"id":"files"})
     * @Required(false)
     */
    public $files = null;
}
