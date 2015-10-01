<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("contact")
 */
class ContactOptional
{
    /**
     * @Form\Attributes({"class":"medium"})
     * @Form\Options({
     *     "label": "Business telephone No."
     * })
     * @Form\Required(false)
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({
     *     "name": "Zend\Validator\Regex",
     *     "options": {
     *         "pattern": "/^[0-9 \(\)\-\+]+$/",
     *         "messages": {
     *             "regexNotMatch": "The input must contain only digits or spaces"
     *         }
     *     }
     * })
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":5,"max":20}})
     * @Form\Name("phone_business")
     */
    public $phoneBusiness = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     * @Form\Name("phone_business_id")
     */
    public $phoneBusinessId = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     * @Form\Name("phone_business_version")
     */
    public $phoneBusinessVersion = null;

    /**
     * @Form\Attributes({"class":"medium"})
     * @Form\Options({
     *     "label": "Home telephone No."
     * })
     * @Form\Required(false)
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({
     *     "name": "Zend\Validator\Regex",
     *     "options": {
     *         "pattern": "/^[0-9 \(\)\-\+]+$/",
     *         "messages": {
     *             "regexNotMatch": "The input must contain only digits or spaces"
     *         }
     *     }
     * })
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":5,"max":20}})
     * @Form\Name("phone_home")
     */
    public $phoneHome = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     * @Form\Name("phone_home_id")
     */
    public $phoneHomeId = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     * @Form\Name("phone_home_version")
     */
    public $phoneHomeVersion = null;

    /**
     * @Form\Attributes({"class":"medium"})
     * @Form\Options({
     *     "label": "Mobile telephone No."
     * })
     * @Form\Required(false)
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({
     *     "name": "Zend\Validator\Regex",
     *     "options": {
     *         "pattern": "/^[0-9 \(\)\-\+]+$/",
     *         "messages": {
     *             "regexNotMatch": "The input must contain only digits or spaces"
     *         }
     *     }
     * })
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":5,"max":20}})
     * @Form\Name("phone_mobile")
     */
    public $phoneMobile = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     * @Form\Name("phone_mobile_id")
     */
    public $phoneMobileId = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     * @Form\Name("phone_mobile_version")
     */
    public $phoneMobileVersion = null;

    /**
     * @Form\Attributes({"class":"medium"})
     * @Form\Options({
     *     "label": "Fax telephone No."
     * })
     * @Form\Required(false)
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({
     *     "name": "Zend\Validator\Regex",
     *     "options": {
     *         "pattern": "/^[0-9 \(\)\-\+]+$/",
     *         "messages": {
     *             "regexNotMatch": "The input must contain only digits or spaces"
     *         }
     *     }
     * })
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":5,"max":20}})
     * @Form\Name("phone_fax")
     */
    public $phoneFax = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     * @Form\Name("phone_fax_id")
     */
    public $phoneFaxId = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     * @Form\Name("phone_fax_version")
     */
    public $phoneFaxVersion = null;

    /**
     * @Form\Attributes({"class":"medium"})
     * @Form\Options({"label":"E-mail address"})
     * @Form\Required(false)
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({"name":"Zend\Validator\EmailAddress"})
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":5,"max":255}})
     */
    public $email = null;
}
