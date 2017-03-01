<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("contact")
 * @Form\Options({
 *     "label": "application_your-business_business-type.contact-details.label",
 *     "hint": "application_your-business_business-type.contact-details.hint"
 * })
 */
class Contact
{
    /**
     * @Form\AllowEmpty(true)
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Validator({
     *      "name": "OneOf",
     *      "options": {
     *          "fields": {"phone_business", "phone_home", "phone_mobile", "phone_fax"},
     *          "message": "At least one telephone number is required"
     *      }
     * })
     * @Form\Name("phone-validator")
     * @Form\Type("Hidden")
     * @Form\Options({
     *     "fieldset-attributes": {
     *         "id":"contact[phone_business]"
     *     }
     * })
     * @Form\Attributes({"value":"1"})
     */
    public $phoneValidator = null;

    /**
     * @Form\Attributes({"class":"medium", "pattern":"\d(\+|\-|\(|\))*","id":"phoneBusiness"})
     * @Form\Options({
     *     "label": "application_your-business_business-type.contact-details.business-phone"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\Phone")
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
     * @Form\Attributes({"class":"medium","pattern":"\d(\+|\-|\(|\))*","id":"phoneHome"})
     * @Form\Options({
     *     "label": "application_your-business_business-type.contact-details.home-phone"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\Phone")
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
     * @Form\Attributes({"class":"medium","pattern":"\d(\+|\-|\(|\))*","id":"phoneMobile"})
     * @Form\Options({
     *     "label": "application_your-business_business-type.contact-details.mobile-phone"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\Phone")
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
     * @Form\Attributes({"class":"medium", "pattern":"\d(\+|\-|\(|\))*","id":"phoneFax"})
     * @Form\Options({
     *     "label": "application_your-business_business-type.contact-details.fax-phone"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\Phone")
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
     * @Form\Attributes({"class":"long","id":"email"})
     * @Form\Options({
     *    "label":"application_your-business_business-type.contact-details.email",
     *    "label_attributes": {
     *        "aria-label": "Business email address"
     *    },
     *     "error-message": "contact_email-error"
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({"name":"Dvsa\Olcs\Transfer\Validators\EmailAddress"})
     */
    public $email = null;
}
