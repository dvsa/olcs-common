<?php

namespace CommonTest\Controller\Plugin;

use Common\Controller\Plugin\CurrentUser;
use Common\Rbac\User;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use ZfcRbac\Service\AuthorizationServiceInterface;

/**
 * Class CurrentUserTest
 * @package CommonTest\Controller\Plugin
 */
class CurrentUserTest extends MockeryTestCase
{
    public function testGetUserData()
    {
        $data = [];

        $userObj = new User();
        $userObj->setUserData($data);

        /** @var AuthorizationServiceInterface|\Mockery\MockInterface $mockAuth */
        $mockAuth = m::mock(AuthorizationServiceInterface::class);
        $mockAuth->shouldReceive('getIdentity')->andReturn($userObj);

        $sut = new CurrentUser($mockAuth);

        static::assertEquals($data, $sut->getUserData());
    }
}
