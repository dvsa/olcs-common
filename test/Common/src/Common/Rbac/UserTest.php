<?php

namespace CommonTest\Rbac;

use Common\Rbac\User;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * Class User
 * @package CommonTest\Rbac
 */
class UserTest extends TestCase
{
    private $sut;


    public function setUp()
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
}
