<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("evidence")
 */
class FinancialEvidenceEvidence
{
    /**
     * @Form\Options({
     *     "fieldset-attributes": {
     *          "class": "checkbox inline",
     *     },
     *     "label": "lva-financial-evidence-upload-now.label",
     *     "value_options": {
     *         "Y":"lva-financial-evidence-upload-now.yes",
     *         "N":"lva-financial-evidence-upload-now.no"
     *     },
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Attributes({"value": "Y"})
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $uploadNow = null;
}
