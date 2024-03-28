<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Laminas\Validator\AbstractValidator;

class StandardAndCabotageYesNoRadioValidator extends AbstractValidator
{
    /** @var Radio */
    private $yesContentElement;

    /**
     * Create service instance
     *
     *
     * @return StandardAndCabotageYesNoRadioValidator
     */
    public function __construct(Radio $yesContentElement)
    {
        $this->yesContentElement = $yesContentElement;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($value, $context = null)
    {
        if ($value == 'Y' && $context['yesContent'] == '') {
            $this->yesContentElement->setMessages(
                ['qanda.bilaterals.standard-and-cabotage.not-selected-message']
            );

            return false;
        }

        return true;
    }
}
