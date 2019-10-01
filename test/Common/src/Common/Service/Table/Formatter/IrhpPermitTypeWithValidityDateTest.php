<?php

namespace CommonTest\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Table\Formatter\IrhpPermitTypeWithValidityDate;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Irhp Permit Type With Validity Date test
 */
class IrhpPermitTypeWithValidityDateTest extends MockeryTestCase
{
    /**
     * @dataProvider scenariosProvider
     */
    public function testFormat($row, $expectedOutput)
    {
        $column = ['name' => 'typeDescription'];

        $sut = new IrhpPermitTypeWithValidityDate();

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->allows('get->translate')
            ->andReturnUsing(
                function ($key) {
                    return '_TRNSLT_' . $key;
                }
            );

        $this->assertEquals(
            $expectedOutput,
            $sut->format($row, $column, $sm)
        );
    }

    public function scenariosProvider()
    {
        return [
            'ECMT Annual - without validity date' => [
                [
                    'typeId' => RefData::ECMT_PERMIT_TYPE_ID,
                    'typeDescription' => 'Annual ECMT>',
                ],
                'Annual ECMT&gt;',
            ],
            'ECMT Annual - with validity date' => [
                [
                    'typeId' => RefData::ECMT_PERMIT_TYPE_ID,
                    'typeDescription' => 'Annual ECMT>',
                    'stockValidTo' => '2019-12-31',
                ],
                'Annual ECMT&gt; 2019',
            ],
            'ECMT Short Term - without validity date' => [
                [
                    'typeId' => RefData::ECMT_SHORT_TERM_PERMIT_TYPE_ID,
                    'typeDescription' => 'Short-term ECMT>',
                ],
                'Short-term ECMT&gt;',
            ],
            'ECMT Short Term - with validity date 2019' => [
                [
                    'typeId' => RefData::ECMT_SHORT_TERM_PERMIT_TYPE_ID,
                    'typeDescription' => 'Short-term ECMT>',
                    'stockValidTo' => '2019-12-31',
                ],
                'Short-term ECMT&gt; 2019',
            ],
            'ECMT Short Term - with validity date 2020' => [
                [
                    'typeId' => RefData::ECMT_SHORT_TERM_PERMIT_TYPE_ID,
                    'typeDescription' => 'Short-term ECMT>',
                    'stockValidTo' => '2020-12-31',
                    'periodNameKey' => 'imATranslationKey'
                ],
                'Short-term ECMT&gt; _TRNSLT_imATranslationKey',
            ],
            'IRHP Bilateral - without validity date' => [
                [
                    'typeId' => RefData::IRHP_BILATERAL_PERMIT_TYPE_ID,
                    'typeDescription' => 'Annual Bilateral>',
                ],
                'Annual Bilateral&gt;',
            ],
            'IRHP Bilateral - with validity date' => [
                [
                    'typeId' => RefData::IRHP_BILATERAL_PERMIT_TYPE_ID,
                    'typeDescription' => 'Annual Bilateral>',
                    'stockValidTo' => '2019-12-31',
                ],
                'Annual Bilateral&gt;',
            ],
            'IRHP Multilateral - without validity date' => [
                [
                    'typeId' => RefData::IRHP_MULTILATERAL_PERMIT_TYPE_ID,
                    'typeDescription' => 'Annual Multilateral>',
                ],
                'Annual Multilateral&gt;',
            ],
            'IRHP Multilateral - with validity date' => [
                [
                    'typeId' => RefData::IRHP_MULTILATERAL_PERMIT_TYPE_ID,
                    'typeDescription' => 'Annual Multilateral>',
                    'stockValidTo' => '2019-12-31',
                ],
                'Annual Multilateral&gt;',
            ],
            'ECMT International Removal - without validity date' => [
                [
                    'typeId' => RefData::ECMT_REMOVAL_PERMIT_TYPE_ID,
                    'typeDescription' => 'ECMT International Removal>',
                ],
                'ECMT International Removal&gt;',
            ],
            'ECMT International Removal - with validity date' => [
                [
                    'typeId' => RefData::ECMT_REMOVAL_PERMIT_TYPE_ID,
                    'typeDescription' => 'ECMT International Removal>',
                    'stockValidTo' => '2019-12-31',
                ],
                'ECMT International Removal&gt;',
            ],
        ];
    }
}
