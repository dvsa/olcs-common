<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Common\Service\Qa\Custom\Common\WarningAdder;
use Zend\Form\Fieldset;

class NiWarningConditionalAdder
{
    /** @var WarningAdder */
    private $warningAdder;

    /**
     * Create service instance
     *
     * @param WarningAdder $warningAdder
     *
     * @return NiWarningConditionalAdder
     */
    public function __construct(WarningAdder $warningAdder)
    {
        $this->warningAdder = $warningAdder;
    }

    /**
     * Add the NI warning to the fieldset if showNiWarning is true
     *
     * @param Fieldset $fieldset
     * @param bool $showNiWarning
     */
    public function addIfRequired(Fieldset $fieldset, $showNiWarning)
    {
        if ($showNiWarning) {
            $this->warningAdder->add(
                $fieldset,
                'permits.page.number-of-trips.northern-ireland.warning',
                WarningAdder::DEFAULT_PRIORITY,
                'niWarning'
            );
        }
    }
}
