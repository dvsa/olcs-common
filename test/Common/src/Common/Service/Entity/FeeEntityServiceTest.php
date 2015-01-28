<?php

/**
 * Fee Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\FeeEntityService;

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
     * @group entity_services
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
    public function testGetOverview()
    {
        $id = 3;

        $this->expectOneRestCall('Fee', 'GET', $id);

        $this->sut->getOverview($id);
    }
}
