<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("data")
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
     *      "fieldset-attributes": {
     *          "class": "question checkbox inline"
     *      },
     *     "label":
     * "selfserve-app-subSection-previous-history-criminal-conviction-hasConv",
     *     "value_options": {
     *         {
     *             "value": "Y",
     *             "label": "Yes",
     *             "label_attributes": {
     *                 "aria-label": "Have you, any person at your company or any of your employees been convicted of a relevant offence?",
     *                 "class" : "inline"
     *             }
     *         },
     *         {
     *             "value": "N",
     *             "label": "No",
     *             "label_attributes": {
     *                 "class" : "inline"
     *             }
     *         }
     *     },
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     * @Form\Validator({"name":"Common\Form\Elements\Validators\LicenceHistoryLicenceValidator",
     *     "options": {
     *         "name": "noConviction"
     *      }
     * })
     */
    public $question = null;

    /**
     * @Form\Name("table")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     */
    public $table = null;
}
