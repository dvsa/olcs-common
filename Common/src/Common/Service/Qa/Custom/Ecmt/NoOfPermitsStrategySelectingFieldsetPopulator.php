<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Common\Service\Qa\FieldsetPopulatorInterface;
use Zend\Form\Fieldset;

class NoOfPermitsStrategySelectingFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var FieldsetPopulatorInterface */
    private $singleEmissionsCategoryFieldsetPopulator;

    /** @var FieldsetPopulatorInterface */
    private $multipleEmissionsCategoryFieldsetPopulator;

    /**
     * Create service instance
     *
     * @param FieldsetPopulatorInterface $singleEmissionsCategoryFieldsetPopulator
     * @param FieldsetPopulatorInterface $multipleEmissionsCategoryFieldsetPopulator
     *
     * @return NoOfPermitsStrategySelectingFieldsetPopulator
     */
    public function __construct(
        FieldsetPopulatorInterface $singleEmissionsCategoryFieldsetPopulator,
        FieldsetPopulatorInterface $multipleEmissionsCategoryFieldsetPopulator
    ) {
        $this->singleEmissionsCategoryFieldsetPopulator = $singleEmissionsCategoryFieldsetPopulator;
        $this->multipleEmissionsCategoryFieldsetPopulator = $multipleEmissionsCategoryFieldsetPopulator;
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
        if (count($options['emissionsCategories']) == 1) {
            $this->singleEmissionsCategoryFieldsetPopulator->populate($form, $fieldset, $options);
            return;
        }

        $this->multipleEmissionsCategoryFieldsetPopulator->populate($form, $fieldset, $options);
    }
}
