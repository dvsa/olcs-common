<?php

namespace CommonTest\Data\Mapper;

use Common\Data\Mapper\LicenceChecklist;
use Common\Service\Helper\TranslationHelperService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\RefData;

/**
 * LicenceChecklist Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceChecklistTest extends MockeryTestCase
{
    /**
     * @dataProvider operatingFromProvider
     */
    public function testMapFromResultToView($key, $description, $code)
    {
        $in = [
            'trafficArea' => [
                'id' => $code,
                'description' => 'Foo'
            ],
            'goodsOrPsv' => [
                'description' => 'Bar'
            ],
            'licenceType' => [
                'description' => 'Cake'
            ],
            'organisation' => [
                'type' => [
                    'description' => 'Limited Company'
                ]
            ]
        ];
        $out = [
            'typeOfLicence' => [
                'operatingFrom' => $description,
                'goodsOrPsv' => 'Bar',
                'licenceType' => 'Cake'
            ],
            'businessType' => [
                'typeOfBusiness' => 'Limited Company'
            ]
        ];
        $mockTranslator = m::mock(TranslationHelperService::class)
            ->shouldReceive('translate')
            ->with($key)
            ->once()
            ->andReturn($description)
            ->getMock();

        $this->assertEquals($out, LicenceChecklist::mapFromResultToView($in, $mockTranslator));
    }

    public function operatingFromProvider()
    {
        return [
            ['continuations.type-of-licence.ni', 'Baz', RefData::NORTHERN_IRELAND_TRAFFIC_AREA_CODE],
            ['continuations.type-of-licence.gb', 'Baz1', 'B']
        ];
    }
}
