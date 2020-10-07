<?php

namespace CommonTest\Service\Qa\Custom\Ecmt;

use Common\Service\Qa\Custom\Ecmt\RestrictedCountriesMultiCheckbox;
use Common\Service\Qa\Custom\Ecmt\YesNoRadioValidator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * YesNoRadioValidatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class YesNoRadioValidatorTest extends MockeryTestCase
{
    private $yesContentElement;

    private $yesNoRadioValidator;

    public function setUp(): void
    {
        $this->yesContentElement = m::mock(RestrictedCountriesMultiCheckbox::class);

        $this->yesNoRadioValidator = new YesNoRadioValidator($this->yesContentElement);
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
                    'yesContent' => ['RU', 'IT']
                ]
            ],
            [
                'value' => 'N',
                'context' => [
                    'yesContent' => ''
                ]
            ]
        ];
    }

    public function testIsValidFalseSetMessages()
    {
        $value = 'Y';
        $context = ['yesContent' => ''];

        $this->yesContentElement->shouldReceive('setMessages')
            ->with(['qanda.ecmt.restricted-countries.error.select-countries'])
            ->once();

        $this->assertFalse(
            $this->yesNoRadioValidator->isValid($value, $context)
        );
    }
}
