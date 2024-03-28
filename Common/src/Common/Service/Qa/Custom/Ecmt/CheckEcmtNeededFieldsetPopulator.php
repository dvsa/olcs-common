<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Common\Service\Qa\CheckboxFieldsetPopulator;
use Common\Service\Qa\FieldsetPopulatorInterface;
use Laminas\Form\Fieldset;

class CheckEcmtNeededFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var CheckboxFieldsetPopulator */
    private $checkboxFieldsetPopulator;

    /** @var InfoIconAdder */
    private $infoIconAdder;

    /**
     * Create service instance
     *
     *
     * @return CheckEcmtNeededFieldsetPopulator
     */
    public function __construct(CheckboxFieldsetPopulator $checkboxFieldsetPopulator, InfoIconAdder $infoIconAdder)
    {
        $this->checkboxFieldsetPopulator = $checkboxFieldsetPopulator;
        $this->infoIconAdder = $infoIconAdder;
    }

    /**
     * Populate the fieldset with elements based on the supplied options array
     *
     * @param mixed $form
     */
    public function populate($form, Fieldset $fieldset, array $options): void
    {
        $this->checkboxFieldsetPopulator->populate($form, $fieldset, $options);

        $this->infoIconAdder->add($fieldset, 'qanda.ecmt.check-ecmt-needed.footer-annotation');
    }
}
