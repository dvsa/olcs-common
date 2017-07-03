<?php

namespace Common\Form\Elements\Types;

/**
 * Radio YesNo form element
 */
class RadioYesNo extends Radio
{
    /**
     * Initial value options
     *
     * @return void
     */
    public function init()
    {
        $this->setValueOptions(['Y' => 'Yes', 'N' => 'No']);

        parent::init();
    }
}
