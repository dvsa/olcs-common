<?php

namespace CommonTest\Common\Form\View\Helper\Extended\Stub;

use Common\Form\View\Helper\Extended\PrepareAttributesTrait;
use Laminas\Form\View\Helper\AbstractHelper;

/**
 * Stub class for testingn PrepareAttributesTrait trait
 */
class PrepareAttributesTraitStub extends AbstractHelper
{
    use PrepareAttributesTrait {
        prepareAttributes as public;
    }

    protected $booleanAttributes = [
        'data-bool-attr' => [
            'on' => 'unit_YES',
            'off' => 'unit_NO',
        ],
    ];

    /**
     * @return bool[]
     *
     * @psalm-return array<string, bool>
     */
    public function getTranslatableAttributes(): array
    {
        return $this->translatableAttributes;
    }
}
