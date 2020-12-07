<?php

namespace Common\Form\Model\Form\Lva\Fieldset\TransportManager;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Attributes({"class":""})
 * @Form\Name("tm-employment-details")
 * @Form\Options({"label": "transport-manager.employment.form.position.title"})
 */
class EmploymentDetails
{
    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $id = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\Attributes({"class":"long"})
     * @Form\Options({
     *     "label":"transport-manager.employment.form.position",
     *     "short-label":"transport-manager.employment.form.position"
     * })
     * @Form\Type("Text")
     * @Form\Required(true)
     * @Form\Validator({
     *     "name":"Laminas\Validator\StringLength",
     *     "options":{
     *          "max":45,
     *     },
     * })
     */
    public $position = null;

    /**
     * @Form\Attributes({
     *     "class":"long",
     *     "placeholder": "transport-manager.employment.form.hoursPerWeek.placeholder",
     * })
     * @Form\Options({
     *     "label":"transport-manager.employment.form.hoursPerWeek",
     *     "short-label":"transport-manager.employment.form.hoursPerWeek",
     *     "error-message": "transport-manager.employment.form.hoursPerWeek.errorMessage",
     * })
     * @Form\Type("Textarea")
     * @Form\Required(true)
     * @Form\Validator({
     *     "name":"Laminas\Validator\StringLength",
     *     "options":{
     *          "max":300,
     *     },
     * })
     */
    public $hoursPerWeek = null;
}
