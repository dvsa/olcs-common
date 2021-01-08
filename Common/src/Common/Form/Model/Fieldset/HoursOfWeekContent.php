<?php

namespace Common\Form\Model\Fieldset;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Name("hoursOfWeekContent")
 * @Form\Type("Laminas\Form\Fieldset")
 */
class HoursOfWeekContent
{
    /**
     * @Form\Required(false)
     * @Form\Filter({"name":"\Laminas\Filter\StringTrim"})
     * @Form\Attributes({
     *     "class": "short",
     *     "data-container-class": "inline-text",
     *     "id": "hoursMon"
     * })
     * @Form\Options({
     *     "label": "days-of-week-short-mon"
     * })
     * @Form\Validator({"name":"Laminas\I18n\Validator\IsFloat", "options": {
     *     "messages": {
     *          "notFloat": "Only numbers are allowed"
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
     * @Form\Required(false)
     * @Form\Filter({"name":"\Laminas\Filter\StringTrim"})
     * @Form\Attributes({
     *     "class": "short",
     *     "data-container-class": "inline-text"
     * })
     * @Form\Options({
     *     "label": "days-of-week-short-tue"
     * })
     * @Form\Validator({"name":"Laminas\I18n\Validator\IsFloat", "options": {
     *     "messages": {
     *          "notFloat": "Only numbers are allowed"
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
     * @Form\Required(false)
     * @Form\Filter({"name":"\Laminas\Filter\StringTrim"})
     * @Form\Attributes({
     *     "class": "short",
     *     "data-container-class": "inline-text"
     * })
     * @Form\Options({
     *     "label": "days-of-week-short-wed"
     * })
     * @Form\Validator({"name":"Laminas\I18n\Validator\IsFloat", "options": {
     *     "messages": {
     *          "notFloat": "Only numbers are allowed"
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
     * @Form\Required(false)
     * @Form\Filter({"name":"\Laminas\Filter\StringTrim"})
     * @Form\Attributes({
     *     "class": "short",
     *     "data-container-class": "inline-text"
     * })
     * @Form\Options({
     *     "label": "days-of-week-short-thu"
     * })
     * @Form\Validator({"name":"Laminas\I18n\Validator\IsFloat", "options": {
     *     "messages": {
     *          "notFloat": "Only numbers are allowed"
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
     * @Form\Required(false)
     * @Form\Filter({"name":"\Laminas\Filter\StringTrim"})
     * @Form\Attributes({
     *     "class": "short",
     *     "data-container-class": "inline-text"
     * })
     * @Form\Options({
     *     "label": "days-of-week-short-fri"
     * })
     * @Form\Validator({"name":"Laminas\I18n\Validator\IsFloat", "options": {
     *     "messages": {
     *          "notFloat": "Only numbers are allowed"
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
     * @Form\Required(false)
     * @Form\Filter({"name":"\Laminas\Filter\StringTrim"})
     * @Form\Attributes({
     *     "class": "short",
     *     "data-container-class": "inline-text"
     * })
     * @Form\Options({
     *     "label": "days-of-week-short-sat"
     * })
     * @Form\Validator({"name":"Laminas\I18n\Validator\IsFloat", "options": {
     *     "messages": {
     *          "notFloat": "Only numbers are allowed"
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
     * @Form\Required(false)
     * @Form\Filter({"name":"\Laminas\Filter\StringTrim"})
     * @Form\Attributes({
     *     "class": "short",
     *     "data-container-class": "inline-text"
     * })
     * @Form\Options({
     *     "label": "days-of-week-short-sun"
     * })
     * @Form\Validator({"name":"Laminas\I18n\Validator\IsFloat", "options": {
     *     "messages": {
     *          "notFloat": "Only numbers are allowed"
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
