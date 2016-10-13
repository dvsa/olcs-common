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

    const TEXT_MIN_LEN = 150;

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::TOO_SHORT => 'FHAdditionalInfo.validation.too_short',
        self::IS_EMPTY => 'FHAdditionalInfo.validation.is_empty',
    );

    /**
     * @var array
     */
    protected $options = array(
        'min' => self::TEXT_MIN_LEN,
    );

    /**
     * @var array
     */
    protected $messageVariables = array(
        'min' => array('options' => 'min'),
    );

    private $validationContextFields = ['bankrupt', 'liquidation', 'receivership', 'administration', 'disqualified'];

    /**
     * Check is valid
     *
     * @param string $value   Field Value
     * @param null   $context Context
     *
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $foundYes = false;
        $elementsToCheck = array_intersect_key($context, array_flip($this->validationContextFields));

        // iterate selected fields to check if yes value was selected
        foreach ($elementsToCheck as $element) {
            if ($element === 'Y') {
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

        // check if value length is at least 150
        $strLenValidator = new ZendValidator\Regex('/^(\S\s?){'.self::TEXT_MIN_LEN.',}$/m');
        if (!$strLenValidator->isValid($value)) {
            $this->error(self::TOO_SHORT);
            return false;
        }

        return true;
    }
}
