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
     * Add a question fieldset to the specified qa fieldset based on the supplied options array
     *
     * @param mixed $form
     * @param array $options
     */
    public function add($form, array $options)
    {
        $fieldset = $this->fieldsetGenerator->generate($options);
        $form->get('qa')->add($fieldset);

        if (count($options['validators'])) {
            $this->validatorsAdder->add($form, $fieldset->getName(), $options['validators']);
        }
    }
}
