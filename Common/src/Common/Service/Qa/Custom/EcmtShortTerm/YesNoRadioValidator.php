<?php

namespace Common\Service\Qa\Custom\EcmtShortTerm;

use Zend\Validator\AbstractValidator;

class YesNoRadioValidator extends AbstractValidator
{
    /** @var mixed */
    private $form;

    /** @var string */
    private $fieldsetName;

    /**
     * Create service instance
     *
     * @param mixed $form
     * @param string $fieldsetName
     *
     * @return YesNoRadioValidator
     */
    public function __construct($form, $fieldsetName)
    {
        $this->form = $form;
        $this->fieldsetName = $fieldsetName;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($value)
    {
        if ($value == 0) {
            $this->form->getInputFilter()->get('qa')->get($this->fieldsetName)->get('yesContent')->setRequired(false);
        }

        return true;
    }
}
