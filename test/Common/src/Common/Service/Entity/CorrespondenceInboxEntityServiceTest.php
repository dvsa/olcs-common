<?php

/**
 * CorrespondenceInboxEntityServiceTest.php
 */
namespace CommonTest\Service\Entity;

use Mockery as m;

use Common\Service\Entity\CorrespondenceInboxEntityService;

/**
 * Class CorrespondenceInboxEntityServiceTest
 *
 * @package CommonTest\Service\Entity
 */
class CorrespondenceInboxEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new CorrespondenceInboxEntityService();

        parent::setUp();
    }

    public function testGetById()
    {
        $this->expectOneRestCall(
            'CorrespondenceInbox',
            'GET',
            1
        )->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getById(1));
    }

    public function testGetCorrespondenceByOrganisation()
    {
        $mockLicenceService = m::mock()
            ->shouldReceive('getList')
            ->andReturn(
                array(
                    'Results' => array(
                        array(
                            'id' => 1
                        )
                    )
                )
            )
            ->getMock();
        $this->sm->setService('Entity\Licence', $mockLicenceService);

        $this->expectOneRestCall(
            'CorrespondenceInbox',
            'GET',
            array('licence' => array(1), 'limit' => 'all')
        )->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getCorrespondenceByOrganisation(1));
    }
}
