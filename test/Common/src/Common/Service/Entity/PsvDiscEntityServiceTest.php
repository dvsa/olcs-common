<?php

/**
 * PsvDisc Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\PsvDiscEntityService;
use CommonTest\Bootstrap;

/**
 * PsvDisc Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvDiscEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new PsvDiscEntityService();

        parent::setUp();
    }

    /**
     * @todo These tests require a real service manager to run, as they are not mocking all dependencies,
     * these tests should be addresses
     */
    protected function getServiceManager()
    {
        return Bootstrap::getRealServiceManager();
    }

    /**
     * @group entity_services
     */
    public function testCeaseDiscs()
    {
        $ids = array(3, 7, 8);

        $date = date('Y-m-d');

        $this->mockDate($date);

        $data = array(
            array(
                'id' => 3,
                'ceasedDate' => $date,
                '_OPTIONS_' => array('force' => true)
            ),
            array(
                'id' => 7,
                'ceasedDate' => $date,
                '_OPTIONS_' => array('force' => true)
            ),
            array(
                'id' => 8,
                'ceasedDate' => $date,
                '_OPTIONS_' => array('force' => true)
            ),
            '_OPTIONS_' => array(
                'multiple' => true
            )
        );

        $this->expectOneRestCall('PsvDisc', 'PUT', $data);

        $this->sut->ceaseDiscs($ids);
    }

    /**
     * @group entity_services
     */
    public function testRequestDiscs()
    {
        $count = 3;
        $data = array(
            'foo' => 'bar'
        );

        $saveData = array(
            '_OPTIONS_' => array(
                'multiple' => true
            ),
            array(
                'foo' => 'bar',
                'isCopy' => 'N'
            ),
            array(
                'foo' => 'bar',
                'isCopy' => 'N'
            ),
            array(
                'foo' => 'bar',
                'isCopy' => 'N'
            )
        );

        $this->expectOneRestCall('PsvDisc', 'POST', $saveData);

        $this->sut->requestDiscs($count, $data);
    }

    /**
     * @group psvDiscEntityService
     */
    public function testGetNotCeasedDiscs()
    {
        $licenceId = 1;
        $query = [
            'ceasedDate' => 'NULL',
            'licence' => $licenceId,
            'limit' => 'all'
        ];

        $this->expectOneRestCall('PsvDisc', 'GET', $query);

        $this->sut->getNotCeasedDiscs($licenceId);
    }
}
