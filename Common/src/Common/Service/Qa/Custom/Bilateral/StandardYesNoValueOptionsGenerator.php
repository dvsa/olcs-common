<?php

namespace Common\Service\Qa\Custom\Bilateral;

class StandardYesNoValueOptionsGenerator
{
    /** @var YesNoValueOptionsGenerator */
    private $yesNoValueOptionsGenerator;

    /**
     * Create service instance
     *
     *
     * @return StandardYesNoValueOptionsGenerator
     */
    public function __construct(YesNoValueOptionsGenerator $yesNoValueOptionsGenerator)
    {
        $this->yesNoValueOptionsGenerator = $yesNoValueOptionsGenerator;
    }

    /**
     * Generate an array of standard value options for a yes/no radio element
     *
     * @return array
     */
    public function generate()
    {
        return $this->yesNoValueOptionsGenerator->generate('Yes', 'No');
    }
}
