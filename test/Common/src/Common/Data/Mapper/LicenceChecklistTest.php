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
    public function testMapFromResultToView($key, $code)
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
                    'description' => 'Limited Company',
                    'id' => RefData::ORG_TYPE_REGISTERED_COMPANY
                ],
                'name' => 'Foo Ltd',
                'companyOrLlpNo' => '12345678'
            ],
            'tradingNames' => [
                [
                    'name' => 'aaa'
                ],
                [
                    'name' => 'bbb'
                ]
            ]
        ];
        $out = [
            'typeOfLicence' => [
                'operatingFrom' => $key . '_translated',
                'goodsOrPsv' => 'Bar',
                'licenceType' => 'Cake'
            ],
            'businessType' => [
                'typeOfBusiness' => 'Limited Company'
            ],
            'businessDetails' => [
                'companyName' => 'Foo Ltd',
                'companyNumber' => '12345678',
                'organisationLabel' => 'continuations.business-details.company-name_translated',
                'tradingNames' => 'aaa, bbb',
            ]
        ];
        $mockTranslator = m::mock(TranslationHelperService::class)
            ->shouldReceive('translate')
            ->andReturnUsing(
                function ($arg) {
                    return $arg . '_translated';
                }
            )
            ->getMock();

        $this->assertEquals($out, LicenceChecklist::mapFromResultToView($in, $mockTranslator));
    }

    public function operatingFromProvider()
    {
        return [
            ['continuations.type-of-licence.ni', RefData::NORTHERN_IRELAND_TRAFFIC_AREA_CODE],
            ['continuations.type-of-licence.gb', 'B']
        ];
    }
}
