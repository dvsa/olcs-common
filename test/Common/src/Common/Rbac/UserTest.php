<?php

namespace CommonTest\Rbac;

use Common\Rbac\User;
use Common\RefData;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * Class User
 * @package CommonTest\Rbac
 */
class UserTest extends TestCase
{
    private $sut;


    public function setUp(): void
    {
        $this->sut = new User();
    }

    public function testIsNotIdentified()
    {

        $this->sut->setUserType(User::USER_TYPE_NOT_IDENTIFIED);

        $this->assertTrue($this->sut->isNotIdentified());
    }

    public function testIsNotIdentifiedFalse()
    {

        $this->sut->setUserType(User::USER_TYPE_ANON);

        $this->assertFalse($this->sut->isNotIdentified());
    }

    public function testHasRole()
    {
        $roles = [RefData::ROLE_INTERNAL_CASE_WORKER];
        $this->sut->setRoles($roles);
        $this->assertEquals($roles, $this->sut->getRoles());
        $this->assertTrue($this->sut->hasRole(RefData::ROLE_INTERNAL_CASE_WORKER));
    }

    public function testHasRoleFalse()
    {
        $roles = [RefData::ROLE_INTERNAL_CASE_WORKER];
        $this->sut->setRoles($roles);
        $this->assertEquals($roles, $this->sut->getRoles());
        $this->assertFalse($this->sut->hasRole(RefData::ROLE_INTERNAL_ADMIN));
    }
}
