<?php

namespace Common\Form\Model\Form\Lva\Fieldset\TransportManager;

use Zend\Form\Annotation as Form;

/**
 * @Form\Attributes({"class":""})
 * @Form\Name("tm-employer-name-details")
 */
class EmployerNameDetails
{
    /**
     * @Form\Attributes({"class":"long"})
     * @Form\Options({
     *     "label":"transport-manager.employment.form.employerName",
     *     "short-label":"transport-manager.employment.form.employerName"
     * })
     * @Form\Type("Text")
     * @Form\Validator({"name": "\Zend\Validator\NotEmpty"})
     */
    public $employerName = null;
}
