<?php

declare(strict_types=1);

namespace CommonTest\Rbac;

use Common\Rbac\Service\Permission;
use Common\Rbac\User;
use Common\RefData;
use LmcRbacMvc\Service\AuthorizationService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class PermissionTest extends MockeryTestCase
{
    private $sut;

    private $authService;

    protected function setUp(): void
    {
        $this->authService = m::mock(AuthorizationService::class);
        $this->sut = new Permission($this->authService);
    }

    public function testIsSelf(): void
    {
        $user = $this->getUser('1');
        $this->authService->expects('getIdentity')->twice()->andReturn($user);

        $this->assertTrue($this->sut->isSelf('1'));
        $this->assertFalse($this->sut->isSelf('2'));
    }

    public function testIsSelfWithIncompleteUserData(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getUserData')->willReturn(['something-else' => 1]);

        $this->authService->expects('getIdentity')->andReturn($user);

        $this->assertFalse($this->sut->isSelf('1'));
    }

    public function testIsInternalUserButNotReadOnly(): void
    {
        $this->authService->expects('isGranted')->with(RefData::PERMISSION_INTERNAL_USER)->andReturnTrue();
        $this->authService->expects('isGranted')->with(RefData::PERMISSION_INTERNAL_EDIT)->andReturnTrue();

        $this->assertFalse($this->sut->isInternalReadOnly());
    }

    public function testIsInternalReadOnlyUser(): void
    {
        $this->authService->expects('isGranted')->with(RefData::PERMISSION_INTERNAL_USER)->andReturnTrue();
        $this->authService->expects('isGranted')->with(RefData::PERMISSION_INTERNAL_EDIT)->andReturnFalse();

        $this->assertTrue($this->sut->isInternalReadOnly());
    }

    public function testNotInternalUser(): void
    {
        $this->authService->expects('isGranted')->with(RefData::PERMISSION_INTERNAL_USER)->andReturnFalse();
        $this->authService->expects('isGranted')->with(RefData::PERMISSION_INTERNAL_EDIT)->never();

        $this->assertFalse($this->sut->isInternalReadOnly());
    }

    public function testCanManageSelfserveUsers(): void
    {
        $this->authService->expects('isGranted')->with(RefData::PERMISSION_CAN_MANAGE_USER_SELFSERVE)->andReturnTrue();
        $this->assertTrue($this->sut->canManageSelfserveUsers());
    }

    public function testCanRemoveSelfserveUserWhenNotSelfserveUser(): void
    {
        $this->authService->expects('isGranted')->with(RefData::PERMISSION_CAN_MANAGE_USER_SELFSERVE)->andReturnFalse();
        $this->assertFalse($this->sut->canRemoveSelfserveUser('userId'));
    }

    public function testCanRemoveSelfserveUserWhenUserIsSelf(): void
    {
        $userId = 'userId1';
        $user = $this->getUser($userId);
        $this->authService->expects('isGranted')->with(RefData::PERMISSION_CAN_MANAGE_USER_SELFSERVE)->andReturnTrue();
        $this->authService->expects('getIdentity')->andReturn($user);

        $this->assertFalse($this->sut->canRemoveSelfserveUser($userId));
    }

    public function testCanRemoveSelfserveUserWhenNotSelf(): void
    {
        $user = $this->getUser('userId1');
        $this->authService->expects('isGranted')->with(RefData::PERMISSION_CAN_MANAGE_USER_SELFSERVE)->andReturnTrue();
        $this->authService->expects('getIdentity')->andReturn($user);

        $this->assertTrue($this->sut->canRemoveSelfserveUser('userId2'));
    }

    /**
     * @dataProvider dpIsGranted
     */
    public function testIsGranted(bool $isGranted): void
    {
        $permission = 'permission';
        $context = 'context';
        $this->authService->expects('isGranted')->with($permission, $context)->andReturn($isGranted);

        $this->assertEquals($isGranted, $this->sut->isGranted($permission, $context));
    }

    public function dpIsGranted(): array
    {
        return [
            [true],
            [false]
        ];
    }

    public function getUser(string $userId): MockObject
    {
        $user = $this->createMock(User::class);
        $user->method('getUserData')->willReturn(['id' => $userId]);

        return $user;
    }
}
