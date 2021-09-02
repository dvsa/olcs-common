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

    /**
     * @dataProvider dpIsLocalAuthority
     */
    public function testIsLocalAuthority($userType, $isLocalAuthority)
    {
        $this->sut->setUserType($userType);
        $this->assertEquals($isLocalAuthority, $this->sut->isLocalAuthority());
    }

    public function dpIsLocalAuthority()
    {
        return [
            [User::USER_TYPE_LOCAL_AUTHORITY, true],
            [User::USER_TYPE_ANON, false],
            [User::USER_TYPE_OPERATOR, false],
            [User::USER_TYPE_PARTNER, false],
            [User::USER_TYPE_TRANSPORT_MANAGER, false],
            [User::USER_TYPE_INTERNAL, false],
            [User::USER_TYPE_NOT_IDENTIFIED, false],
        ];
    }

    /**
     * @dataProvider dpIsNotIdentified
     */
    public function testIsNotIdentified($userType, $isNotIdentified)
    {
        $this->sut->setUserType($userType);
        $this->assertEquals($isNotIdentified, $this->sut->isNotIdentified());
    }

    public function dpIsNotIdentified()
    {
        return [
            [User::USER_TYPE_LOCAL_AUTHORITY, false],
            [User::USER_TYPE_ANON, false],
            [User::USER_TYPE_OPERATOR, false],
            [User::USER_TYPE_PARTNER, false],
            [User::USER_TYPE_TRANSPORT_MANAGER, false],
            [User::USER_TYPE_INTERNAL, false],
            [User::USER_TYPE_NOT_IDENTIFIED, true],
        ];
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
