<?php

namespace PermitsTest\Data\Mapper\Permits;

use Common\Data\Mapper\Permits\BilateralNoOfPermits;
use Common\Data\Mapper\Permits\MultilateralNoOfPermits;
use Common\Data\Mapper\Permits\NoOfPermits;
use Common\Form\Form;
use Common\RefData;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use RuntimeException;

/**
 * NoOfPermitsTest
 */
class NoOfPermitsTest extends TestCase
{
    private $bilateralNoOfPermits;

    private $multilateralNoOfPermits;

    private $noOfPermits;

    private $irhpApplicationDataKey = 'irhpApplication';

    private $maxPermitsByStockDataKey = 'maxPermitsByStock';

    private $feePerPermitDataKey = 'feePerPermit';

    public function setUp()
    {
        $this->bilateralNoOfPermits = m::mock(BilateralNoOfPermits::class);

        $this->multilateralNoOfPermits = m::mock(MultilateralNoOfPermits::class);

        $this->noOfPermits = new NoOfPermits();

        $this->noOfPermits->registerMapper(
            RefData::IRHP_BILATERAL_PERMIT_TYPE_ID,
            $this->bilateralNoOfPermits
        );

        $this->noOfPermits->registerMapper(
            RefData::IRHP_MULTILATERAL_PERMIT_TYPE_ID,
            $this->multilateralNoOfPermits
        );
    }

    public function testMapForFormOptionsMapperExists()
    {
        $data = [
            $this->irhpApplicationDataKey => [
                'irhpPermitType' => [
                    'id' => RefData::IRHP_BILATERAL_PERMIT_TYPE_ID
                ]
            ],
            'otherData' => [
                'key1' => 'value1',
                'key2' => 'value2',
            ]
        ];

        $expectedReturnedData = [
            'returnedDataKey1' => 'returnedDataValue1',
            'returnedDataKey2' => 'returnedDataValue2',
        ];

        $form = m::mock(Form::class);

        $this->bilateralNoOfPermits->shouldReceive('mapForFormOptions')
            ->with(
                $data,
                $form,
                $this->irhpApplicationDataKey,
                $this->maxPermitsByStockDataKey,
                $this->feePerPermitDataKey
            )
            ->andReturn($expectedReturnedData);

        $returnedData = $this->noOfPermits->mapForFormOptions(
            $data,
            $form,
            $this->irhpApplicationDataKey,
            $this->maxPermitsByStockDataKey,
            $this->feePerPermitDataKey
        );

        $this->assertEquals($expectedReturnedData, $returnedData);
    }

    public function testMapForFormOptionsMapperMissing()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unsupported permit type ' . RefData::ECMT_SHORT_TERM_PERMIT_TYPE_ID);

        $data = [
            $this->irhpApplicationDataKey => [
                'irhpPermitType' => [
                    'id' => RefData::ECMT_SHORT_TERM_PERMIT_TYPE_ID
                ]
            ]
        ];

        $form = m::mock(Form::class);

        $this->noOfPermits->mapForFormOptions(
            $data,
            $form,
            $this->irhpApplicationDataKey,
            $this->maxPermitsByStockDataKey,
            $this->feePerPermitDataKey
        );
    }
}
