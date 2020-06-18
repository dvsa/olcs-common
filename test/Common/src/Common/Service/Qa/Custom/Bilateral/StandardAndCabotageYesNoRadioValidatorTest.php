<?php

namespace CommonTest\Service\Qa\Custom\Bilateral;

use Common\Service\Qa\Custom\Bilateral\Radio;
use Common\Service\Qa\Custom\Bilateral\StandardAndCabotageYesNoRadioValidator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * StandardAndCabotageYesNoRadioValidatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class StandardAndCabotageYesNoRadioValidatorTest extends MockeryTestCase
{
    private $yesContentElement;

    private $yesNoRadioValidator;

    public function setUp(): void
    {
        $this->yesContentElement = m::mock(Radio::class);

        $this->yesNoRadioValidator = new StandardAndCabotageYesNoRadioValidator($this->yesContentElement);
    }

    /**
     * @dataProvider dpIsValidTrue
     */
    public function testIsValidTrue($value, $context)
    {
        $this->assertTrue(
            $this->yesNoRadioValidator->isValid($value, $context)
        );
    }

    public function dpIsValidTrue()
    {
        return [
            [
                'value' => 'Y',
                'context' => [
                    'yesContent' => 'yes_content_value'
                ]
            ],
            [
                'value' => 'N',
                'context' => [
                    'yesContent' => null
                ]
            ]
        ];
    }

    public function testIsValidFalseSetMessages()
    {
        $value = 'Y';
        $context = ['yesContent' => null];

        $this->yesContentElement->shouldReceive('setMessages')
            ->with(['qanda.bilaterals.standard-and-cabotage.not-selected-message'])
            ->once();

        $this->assertFalse(
            $this->yesNoRadioValidator->isValid($value, $context)
        );
    }
}
