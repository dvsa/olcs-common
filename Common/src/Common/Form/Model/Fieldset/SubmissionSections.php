<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("submissionSections")
 * @Form\Options({"label":"Select one or more categories"})
 */
class SubmissionSections
{
    /**
     * @Form\Attributes({})
     * @Form\Options({
     *     "label": "Compliance",
     *     "help-block": "Please select a category",
     *     "category": "case_categories_compliance"
     * })
     * @Form\Required(false)
     * @Form\Type("DynamicSelect")
     */
    public $compliance = null;

    /**
     * @Form\Attributes({})
     * @Form\Options({
     *     "label": "TM",
     *     "help-block": "Please select a category",
     *     "category": "case_categories_tm"
     * })
     * @Form\Required(false)
     * @Form\Type("DynamicSelect")
     */
    public $tm = null;

    /**
     * @Form\Attributes({})
     * @Form\Options({
     *     "label": "Licensing application",
     *     "help-block": "Please select a category",
     *     "category": "case_categories_app"
     * })
     * @Form\Required(false)
     * @Form\Type("DynamicSelect")
     */
    public $app = null;

    /**
     * @Form\Attributes({})
     * @Form\Options({
     *     "label": "Licence referral",
     *     "help-block": "Please select a category",
     *     "category": "case_categories_referral"
     * })
     * @Form\Required(false)
     * @Form\Type("DynamicSelect")
     */
    public $referral = null;

    /**
     * @Form\Attributes({})
     * @Form\Options({
     *     "label": "Bus registration",
     *     "help-block": "Please select a category",
     *     "category": "case_categories_bus"
     * })
     * @Form\Required(false)
     * @Form\Type("DynamicSelect")
     */
    public $bus = null;
}
