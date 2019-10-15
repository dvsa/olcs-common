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
     * @param FieldsetAdder $fieldsetAdder
     * @param ValidatorsAdder $validatorsAdder
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
     * @param array $applicationSteps
     */
    public function populate($form, array $applicationSteps)
    {
        foreach ($applicationSteps as $applicationStep) {
            $this->fieldsetAdder->add($form, $applicationStep);
        }

        foreach ($applicationSteps as $applicationStep) {
            $this->validatorsAdder->add($form, $applicationStep);
        }
    }
}
