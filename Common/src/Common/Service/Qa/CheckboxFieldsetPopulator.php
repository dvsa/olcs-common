<?php

namespace Common\Service\Qa;

use Laminas\Form\Fieldset;

class CheckboxFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var CheckboxFactory */
    private $checkboxFactory;

    /** @var TranslateableTextHandler */
    private $translateableTextHandler;

    /**
     * Create service instance
     *
     *
     * @return CheckboxFieldsetPopulator
     */
    public function __construct(
        CheckboxFactory $checkboxFactory,
        TranslateableTextHandler $translateableTextHandler
    ) {
        $this->checkboxFactory = $checkboxFactory;
        $this->translateableTextHandler = $translateableTextHandler;
    }

    /**
     * Populate the fieldset with a checkbox element based on the supplied options array
     *
     * @param mixed $form
     */
    public function populate($form, Fieldset $fieldset, array $options): void
    {
        $label = $this->translateableTextHandler->translate($options['label']);
        $notCheckedMessage = $this->translateableTextHandler->translate($options['notCheckedMessage']);

        $checkbox = $this->checkboxFactory->create('qaElement');
        $checkbox->setAttributes(
            [
                'class' => 'input--qasinglecheckbox',
                'id' => 'qaElement'
            ]
        );
        $checkbox->setLabel($label);
        $checkbox->setLabelAttributes(['class' => 'form-control form-control--checkbox form-control--advanced']);

        $checkbox->setOptions(
            [
                'not_checked_message' => $notCheckedMessage,
                'must_be_value' => '1',
                'checked_value' => '1',
            ]
        );

        $checkbox->setChecked($options['checked']);

        $fieldset->add($checkbox);
    }
}
