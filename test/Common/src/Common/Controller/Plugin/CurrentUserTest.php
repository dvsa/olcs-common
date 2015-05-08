<?php

namespace CommonTest\Controller\Plugin;

use Common\Controller\Plugin\CurrentUser;
use Common\Rbac\User;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Zend\Authentication\AuthenticationServiceInterface;

/**
 * Class CurrentUserTest
 * @package CommonTest\Controller\Plugin
 */
class CurrentUserTest extends TestCase
{
    public function testGetUserData()
    {
        $data = [];

        $userObj = new User();
        $userObj->setUserData($data);

        $mockAuth = m::mock(AuthenticationServiceInterface::class);
        $mockAuth->shouldReceive('getIdentity')->andReturn($userObj);

        $sut = new CurrentUser($mockAuth);

        $this->assertEquals($data, $sut->getUserData());
    }
}
