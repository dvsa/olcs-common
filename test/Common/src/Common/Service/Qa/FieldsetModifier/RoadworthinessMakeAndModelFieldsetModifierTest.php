<?php

namespace CommonTest\Service\Qa\FieldsetModifier;

use Common\Service\Qa\FieldsetModifier\Fieldsets;
use Common\Service\Qa\FieldsetModifier\RoadworthinessMakeAndModelFieldsetModifier;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Element\Text;
use Zend\Form\Fieldset;

/**
 * RoadworthinessMakeAndModelFieldsetModifierTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class RoadworthinessMakeAndModelFieldsetModifierTest extends MockeryTestCase
{
    private $fieldset;

    private $roadworthinessMakeAndModelFieldsetModifier;

    public function setUp(): void
    {
        $this->fieldset = m::mock(Fieldset::class);

        $this->roadworthinessMakeAndModelFieldsetModifier = new RoadworthinessMakeAndModelFieldsetModifier();
    }

    /**
     * @dataProvider dpShouldModify
     */
    public function testShouldModify($fieldsetName, $expectedShouldModify)
    {
        $this->fieldset->shouldReceive('getName')
            ->withNoArgs()
            ->andReturn($fieldsetName);

        $this->assertEquals(
            $expectedShouldModify,
            $this->roadworthinessMakeAndModelFieldsetModifier->shouldModify($this->fieldset)
        );
    }

    public function dpShouldModify()
    {
        return [
            [Fieldsets::ROADWORTHINESS_VEHICLE_MAKE_AND_MODEL, true],
            [Fieldsets::ROADWORTHINESS_TRAILER_MAKE_AND_MODEL, true],
            ['fieldset39', false],
            ['fieldset48', false],
        ];
    }

    public function testModify()
    {
        $text = m::mock(Text::class);

        $this->fieldset->shouldReceive('get')
            ->with('qaElement')
            ->andReturn($text);

        $text->shouldReceive('setAttribute')
            ->with('class', 'govuk-input govuk-input--width-50')
            ->once();

        $this->roadworthinessMakeAndModelFieldsetModifier->modify($this->fieldset);
    }
}
