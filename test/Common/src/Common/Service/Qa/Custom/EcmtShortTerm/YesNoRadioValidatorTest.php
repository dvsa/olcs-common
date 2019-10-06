<?php

namespace CommonTest\Service\Qa\Custom\EcmtShortTerm;

use Common\Service\Qa\Custom\EcmtShortTerm\RestrictedCountriesMultiCheckbox;
use Common\Service\Qa\Custom\EcmtShortTerm\YesNoRadioValidator;
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

    public function setUp()
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
                'value' => 1,
                'context' => [
                    'yesContent' => ['RU', 'IT']
                ]
            ],
            [
                'value' => 0,
                'context' => [
                    'yesContent' => null
                ]
            ]
        ];
    }

    public function testIsValidFalseSetMessages()
    {
        $value = 1;
        $context = ['yesContent' => null];

        $this->yesContentElement->shouldReceive('setMessages')
            ->with(['qanda.ecmt-short-term.restricted-countries.error.select-countries'])
            ->once();

        $this->assertFalse(
            $this->yesNoRadioValidator->isValid($value, $context)
        );
    }
}
