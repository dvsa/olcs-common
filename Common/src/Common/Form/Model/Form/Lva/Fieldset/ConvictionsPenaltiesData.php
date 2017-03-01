<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("data")
 * @Form\Options({
 *     "label": "selfserve-app-subSection-previous-history-criminal-conviction-hasConv-hint",
 *     "label_options": {"disable_html_escape": "true"},
 *     "label_attributes": {"class": "form-element__label field"},
 * })
 */
class ConvictionsPenaltiesData
{
    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "selfserve-app-subSection-previous-history-criminal-conviction-hasConv",
     *     "legend-attributes": {"class": "form-element__question"},
     *     "label_attributes": {"class": "form-control form-control--radio form-control--inline"},
     *     "value_options": {"Y": "Yes", "N": "No"},
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     * @Form\Validator({"name":"Common\Form\Elements\Validators\LicenceHistoryLicenceValidator",
     *     "options": {"name": "noConviction"}
     * })
     */
    public $question = null;

    /**
     * @Form\Name("table")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     */
    public $table = null;
}
