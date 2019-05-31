<?php

namespace CommonTest\Form\View\Helper;

use Common\Service\Qa\FieldsetFactory;
use Common\Service\Qa\FieldsetGenerator;
use Common\Service\Qa\FieldsetPopulatorInterface;
use Common\Service\Qa\FieldsetPopulatorProvider;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Fieldset;

/**
 * FieldsetGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class FieldsetGeneratorTest extends MockeryTestCase
{
    public function testGenerate()
    {
        $fieldsetName = 'fields123';

        $elementType = 'elementType';

        $elementOptions = [
            'elementProperty1' => 'elementValue1',
            'elementProperty2' => 'elementValue2'
        ];

        $options = [
            'fieldsetName' => $fieldsetName,
            'type' => $elementType,
            'element' => $elementOptions
        ];

        $fieldset = m::mock(Fieldset::class);

        $fieldsetFactory = m::mock(FieldsetFactory::class);
        $fieldsetFactory->shouldReceive('create')
            ->with($fieldsetName)
            ->once()
            ->andReturn($fieldset);

        $fieldsetPopulator = m::mock(FieldsetPopulatorInterface::class);
        $fieldsetPopulator->shouldReceive('populate')
            ->with($fieldset, $elementOptions)
            ->once();

        $fieldsetPopulatorProvider = m::mock(FieldsetPopulatorProvider::class);
        $fieldsetPopulatorProvider->shouldReceive('get')
            ->with($elementType)
            ->once()
            ->andReturn($fieldsetPopulator);

        $sut = new FieldsetGenerator($fieldsetPopulatorProvider, $fieldsetFactory);

        $this->assertSame(
            $fieldset,
            $sut->generate($options)
        );
    }
}
