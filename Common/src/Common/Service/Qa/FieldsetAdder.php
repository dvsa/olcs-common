<?php

namespace Common\Service\Qa;

class FieldsetAdder
{
    /** @var FieldsetGenerator */
    private $fieldsetGenerator;

    /** @var ValidatorsAdder */
    private $validatorsAdder;

    /**
     * Create service instance
     *
     * @param FieldsetGenerator $fieldsetGenerator
     * @param ValidatorsAdder $validatorsAdder
     *
     * @return CheckboxFieldsetPopulator
     */
    public function __construct(FieldsetGenerator $fieldsetGenerator, ValidatorsAdder $validatorsAdder)
    {
        $this->fieldsetGenerator = $fieldsetGenerator;
        $this->validatorsAdder = $validatorsAdder;
    }

    /**
     * Add a fieldset to the specified form based on the supplied options array
     *
     * @param mixed Form
     * @param array $options
     */
    public function add($form, array $options)
    {
        $fieldset = $this->fieldsetGenerator->generate($options);
        $form->add($fieldset);
        $this->validatorsAdder->add($form, $fieldset->getName(), $options['validators']);
    }
}
