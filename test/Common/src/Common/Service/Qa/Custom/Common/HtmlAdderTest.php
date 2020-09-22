<?php

namespace CommonTest\Service\Qa\Custom\Common;

use Common\Form\Elements\Types\Html;
use Common\Service\Qa\Custom\Common\HtmlAdder;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;

/**
 * HtmlAdderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class HtmlAdderTest extends MockeryTestCase
{
    public function testAdd()
    {
        $elementName = 'elementName';

        $markup = '<h1>markup</h1>';

        $expectedParams = [
            'name' => $elementName,
            'type' => Html::class,
            'attributes' => [
                'value' => $markup
            ]
        ];

        $fieldset = m::mock(Fieldset::class);
        $fieldset->shouldReceive('add')
            ->with($expectedParams)
            ->once();

        $htmlAdder = new HtmlAdder();
        $htmlAdder->add($fieldset, $elementName, $markup);
    }
}
