<?php

namespace Common\Service\Qa;

class FieldsetPopulator
{
    /** @var FieldsetAdder */
    private $fieldsetAdder;

    /** @var ValidatorsAdder */
    private $validatorsAdder;

    /**
     * Create service instance
     *
     *
     * @return FieldsetPopulator
     */
    public function __construct(FieldsetAdder $fieldsetAdder, ValidatorsAdder $validatorsAdder)
    {
        $this->fieldsetAdder = $fieldsetAdder;
        $this->validatorsAdder = $validatorsAdder;
    }

    /**
     * Populate the specified form with content and validators represented by the supplied application steps array
     *
     * @param mixed $form
     * @param string $usageContext
     */
    public function populate($form, array $applicationSteps, $usageContext): void
    {
        foreach ($applicationSteps as $applicationStep) {
            $this->fieldsetAdder->add($form, $applicationStep, $usageContext);
        }

        foreach ($applicationSteps as $applicationStep) {
            $this->validatorsAdder->add($form, $applicationStep);
        }
    }
}
