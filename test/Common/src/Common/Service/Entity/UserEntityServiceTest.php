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

    /**
     * @group entity_services
     *
     * @NOTE For now we just want to ensure we get the stubbed user, until we implement auth
     */
    public function testGetCurrentUser()
    {
        $id = 1;

        $response = array('RESPONSE');

        $this->expectOneRestCall('User', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals($response, $this->sut->getCurrentUser());
    }
}
