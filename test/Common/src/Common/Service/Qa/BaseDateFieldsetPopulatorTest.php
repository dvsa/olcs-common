<?php

namespace CommonTest\Service\Qa;

use Common\Service\Qa\BaseDateFieldsetPopulator;
use Common\Service\Qa\DateSelect;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;

/**
 * BaseDateFieldsetPopulatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class BaseDateFieldsetPopulatorTest extends MockeryTestCase
{
    public function testPopulate()
    {
        $elementClass = DateSelect::class;
        $elementOptions = ['key1' => 'value1', 'key2' => 'value2'];
        $elementValue = '2020-04-01';

        $expectedFieldsetAddArray = [
            'name' => 'qaElement',
            'type' => $elementClass,
            'options' => $elementOptions,
            'attributes' => [
                'value' => $elementValue
            ]
        ];

        $fieldset = m::mock(Fieldset::class);
        $fieldset->shouldReceive('add')
            ->with($expectedFieldsetAddArray)
            ->once();

        $sut = new BaseDateFieldsetPopulator();
        $sut->populate($fieldset, $elementClass, $elementOptions, $elementValue);
    }
}
