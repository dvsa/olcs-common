<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Name("contact")
 * @Form\Options({
 *     "label": "application_your-business_business-type.contact-details.label",
 *     "hint": "application_your-business_business-type.contact-details.hint"
 * })
 */
class Contact
{

    /**
     * @Form\Required(false)
     * @Form\AllowEmpty(true)
     * @Form\Type("Text")
     */
    public $phoneValidator = null;

    /**
     * @Form\Attributes({"class":"long"})
     * @Form\Options({
     *     "label":
     * "application_your-business_business-type.contact-details.business-phone"
     * })
     * @Form\Required(false)
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({
     *     "name": "Zend\Validator\Regex",
     *     "options": {
     *         "pattern": "/^{0-9 }+$/",
     *         "messages": {
     *             "regexNotMatch": "The input must contain only digits or spaces"
     *         }
     *     }
     * })
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":5,"max":20}})
     */
    public $phoneBusiness = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $phoneBusinessId = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $phoneBusinessVersion = null;

    /**
     * @Form\Attributes({"class":"long"})
     * @Form\Options({
     *     "label":
     * "application_your-business_business-type.contact-details.home-phone"
     * })
     * @Form\Required(false)
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({
     *     "name": "Zend\Validator\Regex",
     *     "options": {
     *         "pattern": "/^{0-9 }+$/",
     *         "messages": {
     *             "regexNotMatch": "The input must contain only digits or spaces"
     *         }
     *     }
     * })
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":5,"max":20}})
     */
    public $phoneHome = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $phoneHomeId = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $phoneHomeVersion = null;

    /**
     * @Form\Attributes({"class":"long"})
     * @Form\Options({
     *     "label":
     * "application_your-business_business-type.contact-details.mobile-phone"
     * })
     * @Form\Required(false)
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({
     *     "name": "Zend\Validator\Regex",
     *     "options": {
     *         "pattern": "/^{0-9 }+$/",
     *         "messages": {
     *             "regexNotMatch": "The input must contain only digits or spaces"
     *         }
     *     }
     * })
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":5,"max":20}})
     */
    public $phoneMobile = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $phoneMobileId = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $phoneMobileVersion = null;

    /**
     * @Form\Attributes({"class":"long"})
     * @Form\Options({
     *     "label": "application_your-business_business-type.contact-details.fax-phone"
     * })
     * @Form\Required(false)
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({
     *     "name": "Zend\Validator\Regex",
     *     "options": {
     *         "pattern": "/^{0-9 }+$/",
     *         "messages": {
     *             "regexNotMatch": "The input must contain only digits or spaces"
     *         }
     *     }
     * })
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":5,"max":20}})
     */
    public $phoneFax = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $phoneFaxId = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $phoneFaxVersion = null;

    /**
     * @Form\Attributes({"class":"long"})
     * @Form\Options({"label":"application_your-business_business-type.contact-details.email"})
     * @Form\Required(false)
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({"name":"Zend\Validator\EmailAddress"})
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":5,"max":255}})
     */
    public $email = null;


}

