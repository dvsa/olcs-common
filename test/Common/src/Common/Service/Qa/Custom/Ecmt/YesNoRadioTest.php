<?php

namespace CommonTest\Service\Qa\Custom\Ecmt;

use Common\Service\Qa\Custom\Ecmt\YesNoRadio;
use DMS\PHPUnitExtensions\ArraySubset\Assert;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * YesNoRadioTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class YesNoRadioTest extends MockeryTestCase
{
    private $yesNoRadio;

    public function setUp(): void
    {
        $this->yesNoRadio = new YesNoRadio();
    }

    public function testAttributes()
    {
        $expectedAttributes = [
            'id' => 'yesNoRadio',
            'radios_wrapper_attributes' => [
                'id' => 'yesNoRadio',
                'class' => 'govuk-radios--conditional',
                'data-module' => 'radios',
            ]
        ];

        $this->assertEquals(
            $expectedAttributes,
            $this->yesNoRadio->getAttributes()
        );
    }

    public function testSetStandardValueOptions()
    {
        $expectedValueOptions = [
            'yes' => [
                'label' => 'Yes',
                'value' => 1,
                'attributes' => [
                    'data-aria-controls' => 'RestrictedCountriesList',
                ],
            ],
            'no' => [
                'label' => 'No',
                'value' => 0,
            ]
        ];

        $this->yesNoRadio->setStandardValueOptions();

        Assert::assertArraySubset(
            $expectedValueOptions,
            $this->yesNoRadio->getValueOptions()
        );
    }
}
