<?php

/**
 * Fee Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\RefData;
use Common\Service\Entity\FeeEntityService;
use Common\Service\Data\FeeTypeDataService;
use Mockery as m;
use Common\Service\Entity\ApplicationEntityService;
use Common\Service\Entity\LicenceEntityService;

/**
 * Fee Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FeeEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new FeeEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetApplication()
    {
        $id = 3;

        $response = array(
            'application' => 1
        );

        $this->expectOneRestCall('Fee', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals(1, $this->sut->getApplication($id));
    }

    /**
     * @group entity_services
     */
    public function testGetApplicationWithoutApplication()
    {
        $id = 3;

        $response = array();

        $this->expectOneRestCall('Fee', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals(null, $this->sut->getApplication($id));
    }

    /**
     * @group entity_services
     */
    public function testGetLatestFeeForBusReg()
    {
        $id = 3;

        $query = array(
            'busReg' => 3,
            'limit' => 1,
            'sort' => 'invoicedDate',
            'order' => 'DESC'
        );

        $response = array(
            'Results' => ['fee1']
        );

        $this->expectOneRestCall('Fee', 'GET', $query)
            ->will($this->returnValue($response));

        $this->assertEquals('fee1', $this->sut->getLatestFeeForBusReg($id));
    }

    /**
     * @group entity_services
     */
    public function testGetOverview()
    {
        $id = 3;

        $this->expectOneRestCall('Fee', 'GET', $id);

        $this->sut->getOverview($id);
    }

    /**
     * @group entity_services
     */
    public function testGetOrganisation()
    {
        $id = 3;

        $organisation = array('id' => 1);

        $response = array(
            'licence' => array(
                'organisation' => $organisation
            )
        );

        $this->expectOneRestCall('Fee', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals($organisation, $this->sut->getOrganisation($id));
    }

    /**
     * @group entity_services
     */
    public function testGetOrganisationWithoutOrganisation()
    {
        $id = 3;

        $response = array();

        $this->expectOneRestCall('Fee', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals(null, $this->sut->getOrganisation($id));
    }

    /**
     * Test get fee by type, statuses and applicationId
     *
     * @group feeEntity
     */
    public function testGetFeeByTypeStatusesAndApplicationId()
    {
        $id = 3;
        $statuses = array(
            RefData::FEE_STATUS_OUTSTANDING,
        );
        $query = array(
            'application' => $id,
            'feeStatus' => $statuses,
            'feeType' => 1,
            'limit' => 'all',
            'sort' => 'createdOn',
            'order' => 'DESC'
        );

        $response = array(
            'Results' => ['fee1']
        );

        $this->expectOneRestCall('Fee', 'GET', $query)
            ->will($this->returnValue($response));

        $this->assertEquals(['fee1'], $this->sut->getFeeByTypeStatusesAndApplicationId(1, $statuses, $id));
    }

    /**
     * Test get fee by type, statuses and applicationId
     *
     * @group feeEntity
     */
    public function testCancelByIds()
    {
        $ids = array(1,2);
        $query = array(
            array(
                'id' => 1,
                'feeStatus' => RefData::FEE_STATUS_CANCELLED,
                '_OPTIONS_' => array('force' => true)
            ),
            array(
                'id' => 2,
                'feeStatus' => RefData::FEE_STATUS_CANCELLED,
                '_OPTIONS_' => array('force' => true)
            ),
            '_OPTIONS_' => array('multiple' => true)
        );
        $this->expectOneRestCall('Fee', 'PUT', $query);
        $this->sut->cancelByIds($ids);
    }

    /**
     * Test get latest fee by type, statuses and applicationId
     *
     * @group feeEntity
     */
    public function testGetLatestFeeByTypeStatusesAndApplicationId()
    {
        $id = 3;
        $statuses = [
            RefData::FEE_STATUS_OUTSTANDING,
        ];

        $query = [
            'application' => $id,
            'feeStatus'   => $statuses,
            'feeType'     => 1,
            'sort'        => 'invoicedDate',
            'order'       => 'DESC',
            'limit'       => 1,
        ];

        $response = [
            'Results' => ['fee1']
        ];

        $this->expectOneRestCall('Fee', 'GET', $query)->will($this->returnValue($response));

        $this->assertEquals('fee1', $this->sut->getLatestFeeByTypeStatusesAndApplicationId(1, $statuses, $id));
    }

    public function testGetOutstandingContinuationFee()
    {
        $expectedQuery = [
            'licence' => 1966,
            'feeStatus' => RefData::FEE_STATUS_OUTSTANDING,
            'limit' => 'all',
        ];
        $expectedBundle = [
            'children' => [
                'feeType' => [
                    'criteria' => [
                        'feeType' => FeeTypeDataService::FEE_TYPE_CONT,
                    ],
                    'required' => true,
                ]
            ]
        ];

        $this->expectOneRestCall('Fee', 'GET', $expectedQuery, $expectedBundle)
            ->will($this->returnValue('RESPONSE'));

        $this->sut->getOutstandingContinuationFee(1966);
    }

    /**
     * @group entity_services
     */
    public function testGetFeeDetailsForInterim()
    {
        $id = 3;

        $response = array();

        $this->expectOneRestCall('Fee', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals([], $this->sut->getFeeDetailsForInterim($id));
    }
}
