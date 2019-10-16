<?php

namespace Common\Service\Qa;

class FieldsetAdder
{
    /** @var FieldsetGenerator */
    private $fieldsetGenerator;

    /**
     * Create service instance
     *
     * @param FieldsetGenerator $fieldsetGenerator
     *
     * @return FieldsetAdder
     */
    public function __construct(FieldsetGenerator $fieldsetGenerator)
    {
        $this->fieldsetGenerator = $fieldsetGenerator;
    }

    /**
     * Add a question fieldset to the specified qa fieldset based on the supplied options array
     *
     * @param mixed $form
     * @param array $options
     */
    public function add($form, array $options)
    {
        $fieldset = $this->fieldsetGenerator->generate($form, $options);
        $form->get('qa')->add($fieldset);
    }
}
