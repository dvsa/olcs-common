<?php

namespace Common\Service\Qa;

class FieldsetGenerator
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
     * @return FieldsetGenerator
     */
    public function __construct(
        FieldsetPopulatorProvider $fieldsetPopulatorProvider,
        FieldsetFactory $fieldsetFactory
    ) {
        $this->fieldsetPopulatorProvider = $fieldsetPopulatorProvider;
        $this->fieldsetFactory = $fieldsetFactory;
    }

    /**
     * Generate and return a populated fieldset based on the supplied options array
     *
     * @param mixed $form
     * @param array $options
     */
    public function generate($form, array $options)
    {
        $fieldset = $this->fieldsetFactory->create($options['fieldsetName'], $options['type']);
        $fieldsetPopulator = $this->fieldsetPopulatorProvider->get($options['type']);
        $fieldsetPopulator->populate($form, $fieldset, $options['element']);

        return $fieldset;
    }
}
