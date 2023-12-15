<?php

namespace CommonTest\Common\Form\View\Helper\Extended\Stub;

use Common\Form\View\Helper\Extended\PrepareAttributesTrait;
use Laminas\Form\View\Helper\AbstractHelper;

/**
 * Stub class for testingn PrepareAttributesTrait trait
 */
class PrepareAttributesTraitStub extends AbstractHelper
{
    protected $booleanAttributes = [
        'data-bool-attr' => [
            'on' => 'unit_YES',
            'off' => 'unit_NO',
        ],
    ];

    use PrepareAttributesTrait {
        prepareAttributes as public;
    }

    public function getTranslatableAttributes()
    {
        return $this->translatableAttributes;
    }
}
