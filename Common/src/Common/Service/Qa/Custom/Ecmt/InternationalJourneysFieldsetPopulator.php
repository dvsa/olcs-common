<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Common\Form\Elements\Types\Html;
use Common\Service\Qa\FieldsetPopulatorInterface;
use Common\Service\Qa\RadioFieldsetPopulator;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Fieldset;

class InternationalJourneysFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var RadioFieldsetPopulator */
    private $radioFieldsetPopulator;

    /** @var NiWarningConditionalAdder */
    private $niWarningConditionalAdder;

    /**
     * Create service instance
     *
     * @param RadioFieldsetPopulator $radioFieldsetPopulator
     * @param NiWarningConditionalAdder $niWarningConditionalAdder
     *
     * @return InternationalJourneysFieldsetPopulator
     */
    public function __construct(
        RadioFieldsetPopulator $radioFieldsetPopulator,
        NiWarningConditionalAdder $niWarningConditionalAdder
    ) {
        $this->radioFieldsetPopulator = $radioFieldsetPopulator;
        $this->niWarningConditionalAdder = $niWarningConditionalAdder;
    }

    /**
     * Populate the fieldset with elements based on the supplied options array
     *
     * @param mixed $form
     * @param Fieldset $fieldset
     * @param array $options
     */
    public function populate($form, Fieldset $fieldset, array $options)
    {
        $this->niWarningConditionalAdder->addIfRequired($fieldset, $options['showNiWarning']);

        $fieldset->add(
            [
                'name' => 'warningVisible',
                'type' => Hidden::class,
                'attributes' => [
                    'value' => 0
                ]
            ]
        );

        $this->radioFieldsetPopulator->populate($form, $fieldset, $options['radio']);
    }
}
