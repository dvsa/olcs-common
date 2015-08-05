<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("hoursOfWeekContent")
 * @Form\Type("Zend\Form\Fieldset")
 */
class HoursOfWeekContentRequired
{
    /**
     * @Form\Type("Text")
     * @Form\AllowEmpty(true)
     * @Form\Attributes({
     *     "class": "short",
     *     "data-container-class": "inline-text"
     * })
     * @Form\Options({
     *     "label": "days-of-week-short-mon"
     * })
     * @Form\Validator({"name": "Common\Form\Elements\Validators\SumContext", "options": {
     *     "min": 1,
     *     "messages": {
     *         "belowMin": "transport-manager-hours-per-week-validation-message"
     *     }
     * }})
     * @Form\Validator({"name":"Digits", "options": {
     *     "messages": {
     *         "notDigits": "Mon must be a whole number"
     *     }
     * }})
     * @Form\Validator({"name":"Between", "options": {
     *     "min": 0,
     *     "max": 24,
     *     "messages": {
     *         "notBetween": "Mon must be between '%min%' and '%max%', inclusively"
     *     }
     * }})
     */
    public $hoursMon = null;

    /**
     * @Form\AllowEmpty(true)
     * @Form\Attributes({
     *     "class": "short",
     *     "data-container-class": "inline-text"
     * })
     * @Form\Options({
     *     "label": "days-of-week-short-tue"
     * })
     * @Form\Validator({"name":"Digits", "options": {
     *     "messages": {
     *         "notDigits": "Tue must be a whole number"
     *     }
     * }})
     * @Form\Validator({"name":"Between", "options": {
     *     "min": 0,
     *     "max": 24,
     *     "messages": {
     *         "notBetween": "Tue must be between '%min%' and '%max%', inclusively"
     *     }
     * }})
     */
    public $hoursTue = null;

    /**
     * @Form\AllowEmpty(true)
     * @Form\Attributes({
     *     "class": "short",
     *     "data-container-class": "inline-text"
     * })
     * @Form\Options({
     *     "label": "days-of-week-short-wed"
     * })
     * @Form\Validator({"name":"Digits", "options": {
     *     "messages": {
     *         "notDigits": "Wed must be a whole number"
     *     }
     * }})
     * @Form\Validator({"name":"Between", "options": {
     *     "min": 0,
     *     "max": 24,
     *     "messages": {
     *         "notBetween": "Wed must be between '%min%' and '%max%', inclusively"
     *     }
     * }})
     */
    public $hoursWed = null;

    /**
     * @Form\AllowEmpty(true)
     * @Form\Attributes({
     *     "class": "short",
     *     "data-container-class": "inline-text"
     * })
     * @Form\Options({
     *     "label": "days-of-week-short-thu"
     * })
     * @Form\Validator({"name":"Digits", "options": {
     *     "messages": {
     *         "notDigits": "Thu must be a whole number"
     *     }
     * }})
     * @Form\Validator({"name":"Between", "options": {
     *     "min": 0,
     *     "max": 24,
     *     "messages": {
     *         "notBetween": "Thu must be between '%min%' and '%max%', inclusively"
     *     }
     * }})
     */
    public $hoursThu = null;

    /**
     * @Form\AllowEmpty(true)
     * @Form\Attributes({
     *     "class": "short",
     *     "data-container-class": "inline-text"
     * })
     * @Form\Options({
     *     "label": "days-of-week-short-fri"
     * })
     * @Form\Validator({"name":"Digits", "options": {
     *     "messages": {
     *         "notDigits": "Fri must be a whole number"
     *     }
     * }})
     * @Form\Validator({"name":"Between", "options": {
     *     "min": 0,
     *     "max": 24,
     *     "messages": {
     *         "notBetween": "Fri must be between '%min%' and '%max%', inclusively"
     *     }
     * }})
     */
    public $hoursFri = null;

    /**
     * @Form\AllowEmpty(true)
     * @Form\Attributes({
     *     "class": "short",
     *     "data-container-class": "inline-text"
     * })
     * @Form\Options({
     *     "label": "days-of-week-short-sat"
     * })
     * @Form\Validator({"name":"Digits", "options": {
     *     "messages": {
     *         "notDigits": "Sat must be a whole number"
     *     }
     * }})
     * @Form\Validator({"name":"Between", "options": {
     *     "min": 0,
     *     "max": 24,
     *     "messages": {
     *         "notBetween": "Sat must be between '%min%' and '%max%', inclusively"
     *     }
     * }})
     */
    public $hoursSat = null;

    /**
     * @Form\AllowEmpty(true)
     * @Form\Attributes({
     *     "class": "short",
     *     "data-container-class": "inline-text"
     * })
     * @Form\Options({
     *     "label": "days-of-week-short-sun"
     * })
     * @Form\Validator({"name":"Digits", "options": {
     *     "messages": {
     *         "notDigits": "Sun must be a whole number"
     *     }
     * }})
     * @Form\Validator({"name":"Between", "options": {
     *     "min": 0,
     *     "max": 24,
     *     "messages": {
     *         "notBetween": "Sun must be between '%min%' and '%max%', inclusively"
     *     }
     * }})
     */
    public $hoursSun = null;
}
