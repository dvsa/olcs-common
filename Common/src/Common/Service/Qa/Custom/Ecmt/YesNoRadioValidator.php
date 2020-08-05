<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Zend\Validator\AbstractValidator;

class YesNoRadioValidator extends AbstractValidator
{
    /** @var RestrictedCountriesMultiCheckbox */
    private $yesContentElement;

    /**
     * Create service instance
     *
     * @param RestrictedCountriesMultiCheckbox $yesContentElement
     *
     * @return YesNoRadioValidator
     */
    public function __construct($yesContentElement)
    {
        $this->yesContentElement = $yesContentElement;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($value, $context = null)
    {
        if ($value == 1 && is_null($context['yesContent'])) {
            $this->yesContentElement->setMessages(
                ['qanda.ecmt.restricted-countries.error.select-countries']
            );
    
            return false;
        }

        return true;
    }
}
