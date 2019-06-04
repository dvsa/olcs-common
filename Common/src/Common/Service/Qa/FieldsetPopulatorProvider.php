<?php

namespace Common\Service\Qa;

use RuntimeException;

class FieldsetPopulatorProvider
{
    /** @var array */
    private $fieldsetPopulators;

    /**
     * Create service instance
     *
     * @return FieldsetPopulatorProvider
     */
    public function __construct()
    {
        $this->fieldsetPopulators = [];
    }

    /**
     * Get an implementation of FieldsetPopulatorInterface corresponding to the supplied form control type
     *
     * @param string $type
     *
     * @throws RuntimeException
     */
    public function get($type)
    {
        if (!isset($this->fieldsetPopulators[$type])) {
            throw new RuntimeException('Fieldset populator not found: ' . $type);
        }

        return $this->fieldsetPopulators[$type];
    }

    /**
     * Add an implementation of FieldsetPopulatorInterface corresponding to the supplied form control type
     *
     * @param string $type
     * @param FieldsetPopulatorInterface $fieldsetPopulator
     */
    public function registerPopulator($type, FieldsetPopulatorInterface $fieldsetPopulator)
    {
        $this->fieldsetPopulators[$type] = $fieldsetPopulator;
    }
}
