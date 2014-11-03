<?php

/**
 * PsvDisc Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\PsvDiscEntityService;

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
}
