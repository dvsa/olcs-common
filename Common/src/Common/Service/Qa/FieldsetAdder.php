<?php

namespace Common\Service\Qa;

class FieldsetAdder
{
    /** @var FieldsetPopulatorProvider */
    private $fieldsetPopulatorProvider;

    /** @var FieldsetFactory */
    private $fieldsetFactory;

    /**
     * Create service instance
     *
     * @param FieldsetPopulatorProvider $fieldsetPopulatorProvider
     * @param FieldsetFactory $fieldsetFactory
     *
     * @return FieldsetAdder
     */
    public function __construct(
        FieldsetPopulatorProvider $fieldsetPopulatorProvider,
        FieldsetFactory $fieldsetFactory
    ) {
        $this->fieldsetPopulatorProvider = $fieldsetPopulatorProvider;
        $this->fieldsetFactory = $fieldsetFactory;
    }

    /**
     * Add a question fieldset to the qa fieldset based on the supplied options array
     *
     * @param mixed $form
     * @param array $options
     * @param string $usageContext
     */
    public function add($form, array $options, $usageContext)
    {
        $fieldset = $this->fieldsetFactory->create($options['fieldsetName'], $options['type']);

        if ($usageContext == UsageContext::CONTEXT_INTERNAL) {
            $fieldset->setLabel($options['shortName']);
        }

        $fieldsetPopulator = $this->fieldsetPopulatorProvider->get($options['type']);
        $fieldsetPopulator->populate($form, $fieldset, $options['element']);

        $form->get('qa')->add($fieldset);
    }
}
