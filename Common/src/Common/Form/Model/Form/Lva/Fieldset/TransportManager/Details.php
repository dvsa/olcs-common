<?php

namespace Common\Form\Model\Form\Lva\Fieldset\TransportManager;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("lva-transport-manager-details")
 */
class Details
{
    /**
     * @Form\Options({"label":"lva-tm-details-details-name"})
     * @Form\Type("\Common\Form\Elements\Types\Readonly")
     */
    public $name = null;

    /**
     * @Form\Options({"label": "lva-tm-details-details-birthDate"})
     * @Form\Type("\Common\Form\Elements\Types\Readonly")
     */
    public $birthDate = null;

    /**
     * @Form\Attributes({"class":"extra-long"})
     * @Form\Options({
     *     "label":"lva-tm-details-details-email",
     *     "short-label": "lva-tm-details-details-email"
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({"name":"Zend\Validator\EmailAddress"})
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":5,"max":255}})
     */
    public $emailAddress = null;

    /**
     * @Form\Attributes({"id":"","class":"medium"})
     * @Form\Options({
     *     "label":"lva-tm-details-details-birthPlace",
     *     "short-label": "lva-tm-details-details-birthPlace"
     * })
     * @Form\Type("Text")
     * @Form\Validator({"name": "\Zend\Validator\NotEmpty"})
     */
    public $birthPlace = null;

    /**
     * @Form\Options({"label":"lva-tm-details-details-certificate"})
     * @Form\Type("\Common\Form\Elements\Types\MultipleFileUpload")
     */
    public $certificate = null;
}
