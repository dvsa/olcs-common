<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * UploadEvidenceFinancialEvidence
 * @Form\Name("financialEvidence")
 */
class UploadEvidenceFinancialEvidence
{
    /**
     * @Form\ComposedObject("\Common\Form\Model\Fieldset\MultipleFileUpload")
     * @Form\Attributes({"id":"files"})
     */
    public $files = null;
}
