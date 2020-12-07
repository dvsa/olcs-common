<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Name("contact")
 * @Form\Options({
 *     "label": "application_your-business_business-type.contact-details.label",
 * })
 */
class Contact
{
    /**
     * @Form\Attributes({"class":"medium","id":"phone_primary"})
     * @Form\Type("\Common\Form\Elements\InputFilters\PhoneRequired")
     */
    public $phone_primary = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $phone_primary_id = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $phone_primary_version = null;

    /**
     * @Form\Attributes({"class":"medium","id":"$phone_secondary"})
     * @Form\Options({"label": "secondary-contact-number-optional"})
     * @Form\Type("\Common\Form\Elements\InputFilters\Phone")
     */
    public $phone_secondary = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $phone_secondary_id = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $phone_secondary_version = null;

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
     * @Form\Filter({"name":"Laminas\Filter\StringTrim"})
     * @Form\Validator({"name":"Dvsa\Olcs\Transfer\Validators\EmailAddress"})
     */
    public $email = null;
}
