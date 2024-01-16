<?php

namespace Common\Form\Model\Fieldset;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Name("contact")
 */
class ContactOptional
{
    /**
     * @Form\Attributes({"class":"medium", "id":"tc_phone_primary"})
     * @Form\Type("\Common\Form\Elements\InputFilters\Phone")
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
     * @Form\Attributes({"class":"medium", "id":"tc_phone_secondary"})
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
     * @Form\Attributes({"class":"long"})
     * @Form\Options({
     *     "label": "email-address-optional",
     *     "error-message": "contactOptional_email-error"
     * })
     * @Form\Required(false)
     * @Form\Type("Text")
     * @Form\Filter("Laminas\Filter\StringTrim")
     * @Form\Validator("Dvsa\Olcs\Transfer\Validators\EmailAddress")
     */
    public $email = null;
}
