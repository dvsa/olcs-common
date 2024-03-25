<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Common\Service\Qa\FieldsetPopulatorInterface;
use Common\Service\Qa\RadioFieldsetPopulator;
use Laminas\Form\Fieldset;

class PermitUsageFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var RadioFieldsetPopulator */
    private $radioFieldsetPopulator;

    /** @var InfoIconAdder */
    private $infoIconAdder;

    /**
     * Create service instance
     *
     *
     * @return PermitUsageFieldsetPopulator
     */
    public function __construct(
        RadioFieldsetPopulator $radioFieldsetPopulator,
        InfoIconAdder $infoIconAdder
    ) {
        $this->radioFieldsetPopulator = $radioFieldsetPopulator;
        $this->infoIconAdder = $infoIconAdder;
    }

    /**
     * Populate the fieldset with elements based on the supplied options array
     *
     * @param mixed $form
     */
    public function populate($form, Fieldset $fieldset, array $options): void
    {
        $this->radioFieldsetPopulator->populate($form, $fieldset, $options);

        $this->infoIconAdder->add($fieldset, 'qanda.ecmt.permit-usage.footer-annotation');
    }
}
