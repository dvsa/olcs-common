<?php

namespace Common\Service\Qa;

use Common\Service\Qa\FieldsetModifier\FieldsetModifier;

class FieldsetAdder
{
    /** @var FieldsetPopulatorProvider */
    private $fieldsetPopulatorProvider;

    /** @var FieldsetFactory */
    private $fieldsetFactory;

    /** @var FieldsetModifier */
    private $fieldsetModifier;

    /**
     * Create service instance
     *
     *
     * @return FieldsetAdder
     */
    public function __construct(
        FieldsetPopulatorProvider $fieldsetPopulatorProvider,
        FieldsetFactory $fieldsetFactory,
        FieldsetModifier $fieldsetModifier
    ) {
        $this->fieldsetPopulatorProvider = $fieldsetPopulatorProvider;
        $this->fieldsetFactory = $fieldsetFactory;
        $this->fieldsetModifier = $fieldsetModifier;
    }

    /**
     * Add a question fieldset to the qa fieldset based on the supplied options array
     *
     * @param mixed $form
     * @param string $usageContext
     */
    public function add($form, array $options, $usageContext): void
    {
        $fieldset = $this->fieldsetFactory->create($options['fieldsetName'], $options['type']);

        $fieldsetPopulator = $this->fieldsetPopulatorProvider->get($options['type']);
        $fieldsetPopulator->populate($form, $fieldset, $options['element']);

        $this->fieldsetModifier->modify($fieldset);

        if ($usageContext == UsageContext::CONTEXT_INTERNAL) {
            $fieldset->setLabel($options['shortName']);
            $fieldset->setLabelAttributes([]);
        }

        $fieldset->setAttribute(
            'data-enabled',
            $options['enabled'] ? 'true' : 'false'
        );

        $form->get('qa')->add($fieldset);
    }
}
