<?php

namespace CommonTest\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Table\Formatter\LicencePermitReference;
use Common\Service\Helper\UrlHelperService as UrlHelper;
use Common\Service\Helper\TranslationHelperService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Licence permit reference test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class LicencePermitReferenceTest extends MockeryTestCase
{
    /**
     * @dataProvider scenariosProvider
     */
    public function testFormat($row, $expectedOutput)
    {
        $urlHelper = m::mock(UrlHelper::class);
        $urlHelper->shouldReceive('fromRoute')
            ->with('permits/application', ['id' => 100])
            ->andReturn('http://selfserve/permits/application/100')
            ->shouldReceive('fromRoute')
            ->with('permits/application/under-consideration', ['id' => 101])
            ->andReturn('http://selfserve/permits/application/101/under-consideration')
            ->shouldReceive('fromRoute')
            ->with('permits/application/awaiting-fee', ['id' => 102])
            ->andReturn('http://selfserve/permits/application/102/awaiting-fee')
            ->shouldReceive('fromRoute')
            ->with('permits/valid', ['licence' => 200, 'type' => $row['typeId']])
            ->andReturn('http://selfserve/permits/valid/105');

        $translator = m::mock(TranslationHelperService::class);
        $translator->shouldReceive('translate')
            ->with('dashboard-table-permit-application-ref')
            ->andReturn('Reference number');

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('Helper\Url')->andReturn($urlHelper);
        $sm->shouldReceive('get')->with('translator')->andReturn($translator);

        $sut = new LicencePermitReference();
        $this->assertEquals(
            $expectedOutput,
            $sut->format($row, null, $sm)
        );
    }

    public function scenariosProvider()
    {
        return [
            'ECMT Annual - not yet submitted' => [
                [
                    'id' => 100,
                    'licenceId' => 200,
                    'licNo' => 'ECMT>',
                    'applicationRef' => 'ECMT>1234567',
                    'typeId' => RefData::ECMT_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_NOT_YET_SUBMITTED,
                ],
                '<span class="visually-hidden">Reference number</span> <a class="overview__link" href="http://selfserve/permits/application/100">' .
                    '<span class="overview__link--underline">ECMT&gt;1234567</span></a>'
            ],
            'ECMT Annual - under consideration' => [
                [   'id' => 101,
                    'licenceId' => 200,
                    'licNo' => 'ECMT>',
                    'applicationRef' => 'ECMT>2345678',
                    'typeId' => RefData::ECMT_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_UNDER_CONSIDERATION,
                ],
                '<span class="visually-hidden">Reference number</span> <a class="overview__link" href="http://selfserve/permits/application/101/under-consideration">' .
                    '<span class="overview__link--underline">ECMT&gt;2345678</span></a>'
            ],
            'ECMT Annual - awaiting fee' => [
                [
                    'id' => 102,
                    'licenceId' => 200,
                    'licNo' => 'ECMT>',
                    'applicationRef' => 'ECMT>3456789',
                    'typeId' => RefData::ECMT_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_AWAITING_FEE,
                ],
                '<span class="visually-hidden">Reference number</span> <a class="overview__link" href="http://selfserve/permits/application/102/awaiting-fee">' .
                    '<span class="overview__link--underline">ECMT&gt;3456789</span></a>'
            ],
            'ECMT Annual - fee paid' => [
                [
                    'id' => 8,
                    'licenceId' => 200,
                    'licNo' => 'ECMT>',
                    'applicationRef' => 'ECMT>3456789',
                    'typeId' => RefData::ECMT_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_FEE_PAID,
                ],
                '<span class="visually-hidden">Reference number</span> ECMT&gt;3456789'
            ],
            'ECMT Annual - issuing' => [
                [
                    'id' => 8,
                    'licenceId' => 200,
                    'licNo' => 'ECMT>',
                    'applicationRef' => 'ECMT>3456789',
                    'typeId' => RefData::ECMT_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_ISSUING,
                ],
                '<span class="visually-hidden">Reference number</span> ECMT&gt;3456789'
            ],
            'ECMT Annual - valid' => [
                [
                    'id' => 105,
                    'licenceId' => 200,
                    'licNo' => 'ECMT>',
                    'applicationRef' => 'ECMT>4567890',
                    'typeId' => RefData::ECMT_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_VALID,
                ],
                '<span class="visually-hidden">Reference number</span> <a class="overview__link" href="http://selfserve/permits/valid/105">' .
                    '<span class="overview__link--underline">ECMT&gt;</span></a>'
            ],
            'ECMT Short Term app - not yet submitted' => [
                [
                    'id' => 100,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC100',
                    'typeId' => RefData::ECMT_SHORT_TERM_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_NOT_YET_SUBMITTED,
                ],
                '<span class="visually-hidden">Reference number</span> <a class="overview__link" href="http://selfserve/permits/application/100">' .
                    '<span class="overview__link--underline">IRHP&gt;ABC100</span></a>'
            ],
            'ECMT Short Term app - under consideration' => [
                [
                    'id' => 101,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC101',
                    'typeId' => RefData::ECMT_SHORT_TERM_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_UNDER_CONSIDERATION,
                ],
                '<span class="visually-hidden">Reference number</span> <a class="overview__link" href="http://selfserve/permits/application/101/under-consideration">' .
                    '<span class="overview__link--underline">IRHP&gt;ABC101</span></a>'
            ],
            'ECMT Short Term app - awaiting fee' => [
                [
                    'id' => 102,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC102',
                    'typeId' => RefData::ECMT_SHORT_TERM_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_AWAITING_FEE,
                ],
                '<span class="visually-hidden">Reference number</span> <a class="overview__link" href="http://selfserve/permits/application/102/awaiting-fee">' .
                    '<span class="overview__link--underline">IRHP&gt;ABC102</span></a>'
            ],
            'ECMT Short Term app - fee paid' => [
                [
                    'id' => 103,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC103',
                    'typeId' => RefData::ECMT_SHORT_TERM_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_FEE_PAID,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;ABC103'
            ],
            'ECMT Short Term app - issuing' => [
                [
                    'id' => 104,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC104',
                    'typeId' => RefData::ECMT_SHORT_TERM_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_ISSUING,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;ABC104'
            ],
            'ECMT Short Term app - valid' => [
                [
                    'id' => 105,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC105',
                    'typeId' => RefData::ECMT_SHORT_TERM_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_VALID,
                ],
                '<span class="visually-hidden">Reference number</span> <a class="overview__link" href="http://selfserve/permits/valid/105">' .
                    '<span class="overview__link--underline">IRHP&gt;</span></a>'
            ],
            'IRHP Bilateral app - not yet submitted' => [
                [
                    'id' => 100,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC100',
                    'typeId' => RefData::IRHP_BILATERAL_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_NOT_YET_SUBMITTED,
                ],
                '<span class="visually-hidden">Reference number</span> <a class="overview__link" href="http://selfserve/permits/application/100">' .
                    '<span class="overview__link--underline">IRHP&gt;ABC100</span></a>'
            ],
            'IRHP Bilateral app - under consideration' => [
                [
                    'id' => 101,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC101',
                    'typeId' => RefData::IRHP_BILATERAL_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_UNDER_CONSIDERATION,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;ABC101'
            ],
            'IRHP Bilateral app - awaiting fee' => [
                [
                    'id' => 102,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC102',
                    'typeId' => RefData::IRHP_BILATERAL_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_AWAITING_FEE,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;ABC102'
            ],
            'IRHP Bilateral app - fee paid' => [
                [
                    'id' => 103,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC103',
                    'typeId' => RefData::IRHP_BILATERAL_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_FEE_PAID,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;ABC103'
            ],
            'IRHP Bilateral app - issuing' => [
                [
                    'id' => 104,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC104',
                    'typeId' => RefData::IRHP_BILATERAL_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_ISSUING,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;ABC104'
            ],
            'IRHP Bilateral app - valid' => [
                [
                    'id' => 105,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC105',
                    'typeId' => RefData::IRHP_BILATERAL_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_VALID,
                ],
                '<span class="visually-hidden">Reference number</span> <a class="overview__link" href="http://selfserve/permits/valid/105">' .
                    '<span class="overview__link--underline">IRHP&gt;</span></a>'
            ],
            'IRHP Multilateral app - not yet submitted' => [
                [
                    'id' => 100,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC100',
                    'typeId' => RefData::IRHP_MULTILATERAL_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_NOT_YET_SUBMITTED,
                ],
                '<span class="visually-hidden">Reference number</span> <a class="overview__link" href="http://selfserve/permits/application/100">' .
                    '<span class="overview__link--underline">IRHP&gt;ABC100</span></a>'
            ],
            'IRHP Multilateral app - under consideration' => [
                [
                    'id' => 101,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC101',
                    'typeId' => RefData::IRHP_MULTILATERAL_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_UNDER_CONSIDERATION,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;ABC101'
            ],
            'IRHP Multilateral app - awaiting fee' => [
                [
                    'id' => 102,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC102',
                    'typeId' => RefData::IRHP_MULTILATERAL_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_AWAITING_FEE,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;ABC102'
            ],
            'IRHP Multilateral app - fee paid' => [
                [
                    'id' => 103,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC103',
                    'typeId' => RefData::IRHP_MULTILATERAL_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_FEE_PAID,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;ABC103'
            ],
            'IRHP Multilateral app - issuing' => [
                [
                    'id' => 104,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC104',
                    'typeId' => RefData::IRHP_MULTILATERAL_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_ISSUING,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;ABC104'
            ],
            'IRHP Multilateral app - valid' => [
                [
                    'id' => 105,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC105',
                    'typeId' => RefData::IRHP_MULTILATERAL_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_VALID,
                ],
                '<span class="visually-hidden">Reference number</span> <a class="overview__link" href="http://selfserve/permits/valid/105">' .
                    '<span class="overview__link--underline">IRHP&gt;</span></a>'
            ],
            'IRHP Ecmt removal - not yet submitted' => [
                [
                    'id' => 100,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC100',
                    'typeId' => RefData::ECMT_REMOVAL_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_NOT_YET_SUBMITTED,
                ],
                '<span class="visually-hidden">Reference number</span> <a class="overview__link" href="http://selfserve/permits/application/100">' .
                '<span class="overview__link--underline">IRHP&gt;ABC100</span></a>'
            ],
            'IRHP Ecmt removal - under consideration' => [
                [
                    'id' => 101,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC101',
                    'typeId' => RefData::ECMT_REMOVAL_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_UNDER_CONSIDERATION,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;ABC101'
            ],
            'IRHP Ecmt removal - awaiting fee' => [
                [
                    'id' => 102,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC102',
                    'typeId' => RefData::ECMT_REMOVAL_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_AWAITING_FEE,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;ABC102'
            ],
            'IRHP Ecmt removal - fee paid' => [
                [
                    'id' => 103,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC103',
                    'typeId' => RefData::ECMT_REMOVAL_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_FEE_PAID,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;ABC103'
            ],
            'IRHP Ecmt removal - issuing' => [
                [
                    'id' => 104,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC104',
                    'typeId' => RefData::ECMT_REMOVAL_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_ISSUING,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;ABC104'
            ],
            'IRHP Ecmt removal - valid' => [
                [
                    'id' => 105,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC105',
                    'typeId' => RefData::ECMT_REMOVAL_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_VALID,
                ],
                '<span class="visually-hidden">Reference number</span> <a class="overview__link" href="http://selfserve/permits/valid/105">' .
                '<span class="overview__link--underline">IRHP&gt;</span></a>'
            ],
            'Certificate of Roadworthiness for vehicle - not yet submitted' => [
                [
                    'id' => 100,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC100',
                    'typeId' => RefData::CERT_ROADWORTHINESS_VEHICLE_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_NOT_YET_SUBMITTED,
                ],
                '<span class="visually-hidden">Reference number</span> <a class="overview__link" href="http://selfserve/permits/application/100">' .
                '<span class="overview__link--underline">IRHP&gt;ABC100</span></a>'
            ],
            'Certificate of Roadworthiness for vehicle - under consideration' => [
                [
                    'id' => 101,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC101',
                    'typeId' => RefData::CERT_ROADWORTHINESS_VEHICLE_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_UNDER_CONSIDERATION,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;ABC101'
            ],
            'Certificate of Roadworthiness for vehicle - awaiting fee' => [
                [
                    'id' => 102,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC102',
                    'typeId' => RefData::CERT_ROADWORTHINESS_VEHICLE_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_AWAITING_FEE,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;ABC102'
            ],
            'Certificate of Roadworthiness for vehicle - fee paid' => [
                [
                    'id' => 103,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC103',
                    'typeId' => RefData::CERT_ROADWORTHINESS_VEHICLE_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_FEE_PAID,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;ABC103'
            ],
            'Certificate of Roadworthiness for vehicle - issuing' => [
                [
                    'id' => 104,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC104',
                    'typeId' => RefData::CERT_ROADWORTHINESS_VEHICLE_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_ISSUING,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;ABC104'
            ],
            'Certificate of Roadworthiness for vehicle - valid' => [
                [
                    'id' => 105,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC105',
                    'typeId' => RefData::CERT_ROADWORTHINESS_VEHICLE_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_VALID,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;'
            ],
            'Certificate of Roadworthiness for trailer - not yet submitted' => [
                [
                    'id' => 100,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC100',
                    'typeId' => RefData::CERT_ROADWORTHINESS_TRAILER_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_NOT_YET_SUBMITTED,
                ],
                '<span class="visually-hidden">Reference number</span> <a class="overview__link" href="http://selfserve/permits/application/100">' .
                '<span class="overview__link--underline">IRHP&gt;ABC100</span></a>'
            ],
            'Certificate of Roadworthiness for trailer - under consideration' => [
                [
                    'id' => 101,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC101',
                    'typeId' => RefData::CERT_ROADWORTHINESS_TRAILER_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_UNDER_CONSIDERATION,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;ABC101'
            ],
            'Certificate of Roadworthiness for trailer - awaiting fee' => [
                [
                    'id' => 102,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC102',
                    'typeId' => RefData::CERT_ROADWORTHINESS_TRAILER_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_AWAITING_FEE,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;ABC102'
            ],
            'Certificate of Roadworthiness for trailer - fee paid' => [
                [
                    'id' => 103,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC103',
                    'typeId' => RefData::CERT_ROADWORTHINESS_TRAILER_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_FEE_PAID,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;ABC103'
            ],
            'Certificate of Roadworthiness for trailer - issuing' => [
                [
                    'id' => 104,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC104',
                    'typeId' => RefData::CERT_ROADWORTHINESS_TRAILER_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_ISSUING,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;ABC104'
            ],
            'Certificate of Roadworthiness for trailer - valid' => [
                [
                    'id' => 105,
                    'licenceId' => 200,
                    'licNo' => 'IRHP>',
                    'applicationRef' => 'IRHP>ABC105',
                    'typeId' => RefData::CERT_ROADWORTHINESS_TRAILER_PERMIT_TYPE_ID,
                    'statusId' => RefData::PERMIT_APP_STATUS_VALID,
                ],
                '<span class="visually-hidden">Reference number</span> IRHP&gt;'
            ],
        ];
    }
}
