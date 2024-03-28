<?php

namespace CommonTest\Common\Form\View\Helper\Extended;

use CommonTest\Common\Form\View\Helper\Extended\Stub\PrepareAttributesTraitStub;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Common\Form\View\Helper\Extended\PrepareAttributesTrait
 */
class PrepareAttributesTraitTest extends MockeryTestCase
{
    public function test(): void
    {
        $sut = new PrepareAttributesTraitStub();

        $attb = [
            'title' => 'unit_Title',
            'type' => 'element_type',
            'name' => 'data[unit_name]',
            'disabled' => 'off',
            'unit-attribute' => false,
            'data-bool-Attr' => ['on' => 1],
            'x-unit' => 'unit_x-unit_Value',
            'DATA-unit' => 'unit_data-unit_Value',
            'aria-unit' => 'unit_aria-unut_Value',
        ];

        static::assertEquals(
            [
                'title' => 'unit_Title',
                'x-unit' => 'unit_x-unit_Value',
                'data-unit' => 'unit_data-unit_Value',
                'aria-unit' => 'unit_aria-unut_Value',
                'data-bool-attr' => 'unit_YES',
            ],
            $sut->prepareAttributes($attb)
        );
    }
}
