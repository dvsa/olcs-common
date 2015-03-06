<?php

/**
 * Fee Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\FeeEntityService;
use Mockery as m;

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
    public function testCancelForLicenceWithNoResults()
    {
        $id = 3;

        $query = array(
            'licence' => $id,
            'feeStatus' => array(
                FeeEntityService::STATUS_OUTSTANDING,
                FeeEntityService::STATUS_WAIVE_RECOMMENDED
            ),
            'limit' => 'all'
        );

        $results = array('Results' => array());

        $this->expectOneRestCall('Fee', 'GET', $query)
            ->will($this->returnValue($results));

        $this->sut->cancelForLicence($id);
    }

    /**
     * @group feeService
     */
    public function testCancelForLicence()
    {
        $id = 3;

        $query = array(
            'licence' => $id,
            'feeStatus' => array(
                FeeEntityService::STATUS_OUTSTANDING,
                FeeEntityService::STATUS_WAIVE_RECOMMENDED
            ),
            'limit' => 'all'
        );

        $results = array(
            'Results' => array(
                array(
                    'id' => 7,
                    'task' => array(
                        'id' => 1,
                        'version' => 1
                    )
                )
            )
        );

        $data = array(
            '_OPTIONS_' => array('multiple' => true),
            array(
                'id' => 7,
                'feeStatus' => FeeEntityService::STATUS_CANCELLED,
                '_OPTIONS_' => array('force' => true)
            )
        );
        $taskData = array(
            array(
                'id' => 1,
                'isClosed' => 'Y',
                'version' => 1
            )
        );

        $this->expectedRestCallInOrder('Fee', 'GET', $query)
            ->will($this->returnValue($results));

        $this->expectedRestCallInOrder('Fee', 'PUT', $data);

        $mockTaskService = m::mock()
            ->shouldReceive('multiUpdate')
            ->with($taskData)
            ->getMock();
        $this->sm->setService('Entity\Task', $mockTaskService);

        $this->sut->cancelForLicence($id);
    }

    /**
     * @group entity_services
     */
    public function testCancelForApplicationWithNoResults()
    {
        $id = 123;

        $query = array(
            'application' => $id,
            'feeStatus' => array(
                FeeEntityService::STATUS_OUTSTANDING,
                FeeEntityService::STATUS_WAIVE_RECOMMENDED
            ),
            'limit' => 'all'
        );

        $results = array('Results' => array());

        $this->expectOneRestCall('Fee', 'GET', $query)
            ->will($this->returnValue($results));

        $this->sut->cancelForApplication($id);
    }

    /**
     * @group entity_services
     */
    public function testCancelForApplication()
    {
        $id = 123;

        $query = array(
            'application' => $id,
            'feeStatus' => array(
                FeeEntityService::STATUS_OUTSTANDING,
                FeeEntityService::STATUS_WAIVE_RECOMMENDED
            ),
            'limit' => 'all'
        );

        $results = array(
            'Results' => array(
                array(
                    'id' => 7
                )
            )
        );

        $data = array(
            '_OPTIONS_' => array('multiple' => true),
            array(
                'id' => 7,
                'feeStatus' => FeeEntityService::STATUS_CANCELLED,
                '_OPTIONS_' => array('force' => true)
            )
        );

        $this->expectedRestCallInOrder('Fee', 'GET', $query)
            ->will($this->returnValue($results));

        $this->expectedRestCallInOrder('Fee', 'PUT', $data);

        $this->sut->cancelForApplication($id);
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
    public function testGetOutstandingFeesForApplication()
    {
        $id = 3;

        $query = array(
            'application' => 3,
            'feeStatus' => array(
                FeeEntityService::STATUS_OUTSTANDING,
                FeeEntityService::STATUS_WAIVE_RECOMMENDED
            ),
            'limit' => 'all'
        );

        $response = array(
            'Results' => 'RESPONSE'
        );

        $this->expectOneRestCall('Fee', 'GET', $query)
            ->will($this->returnValue($response));

        $this->assertEquals('RESPONSE', $this->sut->getOutstandingFeesForApplication($id));
    }

    /**
     * @group entity_services
     */
    public function testGetLatestOutstandingFeeForApplication()
    {
        $id = 3;

        $query = array(
            'application' => 3,
            'feeStatus' => array(
                FeeEntityService::STATUS_OUTSTANDING,
                FeeEntityService::STATUS_WAIVE_RECOMMENDED
            ),
            'limit' => 1,
            'sort' => 'invoicedDate',
            'order' => 'DESC'
        );

        $response = array(
            'Results' => ['fee1']
        );

        $this->expectOneRestCall('Fee', 'GET', $query)
            ->will($this->returnValue($response));

        $this->assertEquals('fee1', $this->sut->getLatestOutstandingFeeForApplication($id));
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
}
