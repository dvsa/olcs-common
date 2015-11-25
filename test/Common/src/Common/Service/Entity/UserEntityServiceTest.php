<?php

/**
 * User Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\UserEntityService;

/**
 * User Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UserEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new UserEntityService();

        parent::setUp();
    }

    public function testGetUserDetails()
    {
        $this->expectOneRestCall('User', 'GET', 111)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getUserDetails(111));
    }

    public function testGetTransportManagerApplications()
    {
        $this->expectOneRestCall('User', 'GET', ['id' => 54, 'limit' => 'all'])
            ->will($this->returnValue(['transportManager' => ['tmApplications' => ['data']]]));

        $this->assertEquals(['data'], $this->sut->getTransportManagerApplications(54));
    }
}
