<?php

/**
 * Custom validator for Finacial History additional info.
 *
 * There is a context dependency on other form fields.
 * So if any of first five questions is set to 'yes', then validation is enabled
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator as ZendValidator;

/**
 * Custom validator for Finacial History additional info.
 *
 * There is a context dependency on other form fields.
 * So if any of first five questions is set to 'yes', then validation is enabled
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class FHAdditionalInfo extends ZendValidator\AbstractValidator
{
    const TOO_SHORT = 'stringLengthTooShort';
    const IS_EMPTY  = 'isEmpty';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::TOO_SHORT => 'You selected \'yes\' in one of the provided questions, so the input has to be at least %min%
            characters long',
        self::IS_EMPTY => 'FHAdditionalInfo.validation.is_empty'
    );

    /**
     * @var array
     */
    protected $options = array(
        'min' => 200
    );

    /**
     * @var array
     */
    protected $messageVariables = array(
        'min' => array('options' => 'min'),
    );

    private $validationContextFields = ['bankrupt', 'liquidation', 'receivership', 'administration', 'disqualified'];

    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $foundYes = false;
        $elementsToCheck = array_intersect_key($context, array_flip($this->validationContextFields));

        // iterate selected fields to check if yes value was selected
        foreach ($elementsToCheck as $element) {
            if ($element == 'Y') {
                $foundYes = true;
                break;
            }
        }

        // all fields are set to No, so no need to fill additional data element
        if (!$foundYes) {
            return true;
        }

        // check if value is not empty
        $notEmptyValidator = new ZendValidator\NotEmpty();
        if (!$notEmptyValidator->isValid($value)) {
            $this->error(self::IS_EMPTY);
            return false;
        }

        // check if value length is at least 200
        $strLenValidator = new ZendValidator\StringLength(array('min' => 200));
        if (!$strLenValidator->isValid($value)) {
            $this->error(self::TOO_SHORT);
            return false;
        }

        return true;
    }
}
