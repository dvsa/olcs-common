<?php

declare(strict_types=1);

namespace CommonTest\Rbac;

use Common\Rbac\Service\Permission;
use Common\RefData;
use LmcRbacMvc\Service\AuthorizationService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class PermissionTest extends MockeryTestCase
{
    private $sut;
    private $authService;

    public function setUp(): void
    {
        $this->authService = m::mock(AuthorizationService::class);
        $this->sut = new Permission($this->authService);
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
}

