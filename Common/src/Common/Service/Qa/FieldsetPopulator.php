<?php

namespace Common\Service\Qa;

class FieldsetPopulator
{
    /**
     * Create service instance
     *
     *
     * @return FieldsetPopulator
     */
    public function __construct(private FieldsetAdder $fieldsetAdder, private ValidatorsAdder $validatorsAdder)
    {
    }

    /**
     * Populate the specified form with content and validators represented by the supplied application steps array
     *
     * @param string $usageContext
     */
    public function populate(mixed $form, array $applicationSteps, $usageContext): void
    {
        foreach ($applicationSteps as $applicationStep) {
            $this->fieldsetAdder->add($form, $applicationStep, $usageContext);
        }

        foreach ($applicationSteps as $applicationStep) {
            $this->validatorsAdder->add($form, $applicationStep);
        }
    }
}
