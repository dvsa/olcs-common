<?php

namespace CommonTest\Service\Qa\Custom\EcmtShortTerm;

use Common\Service\Qa\Custom\EcmtShortTerm\YesNoRadio;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * YesNoRadioTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class YesNoRadioTest extends MockeryTestCase
{
    private $yesNoRadio;

    public function setUp()
    {
        $this->yesNoRadio = new YesNoRadio();
    }

    public function testAttributes()
    {
        $expectedAttributes = [
            'radios_wrapper_attributes' => [
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

        $this->assertArraySubset(
            $expectedValueOptions,
            $this->yesNoRadio->getValueOptions()
        );
    }
}
