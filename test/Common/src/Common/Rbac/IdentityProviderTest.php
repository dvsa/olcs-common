<?php

namespace CommonTest\Rbac;

use Common\Rbac\User;
use Common\Rbac\IdentityProvider;
use Common\Service\Cqrs\Query\QuerySender;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;

/**
 * Class IdentityProviderTest
 * @package CommonTest\Rbac
 */
class IdentityProviderTest extends TestCase
{
    private $sut;

    private $queryService;

    public function setUp()
    {
        $this->queryService = m::mock(QuerySender::class);

        $this->sut = new IdentityProvider($this->queryService);
    }

    public function testGetIdentity()
    {
        $data = [
            'id' => 22,
            'pid' => '12345abc',
            'userType' => User::USER_TYPE_OPERATOR,
            'loginId' => 'username',
            'roles' => [
                ['role' => 'role1'],
                ['role' => 'role2'],
            ]
        ];

        $mockResponse = m::mock();
        $mockResponse->shouldReceive('isOk')->andReturn(true);
        $mockResponse->shouldReceive('getResult')->andReturn($data);

        $this->queryService->shouldReceive('send')->once()->andReturn($mockResponse);

        $identity = $this->sut->getIdentity();
        $this->assertInstanceOf(User::class, $identity);
        $this->assertEquals($data['id'], $identity->getId());
        $this->assertEquals($data['pid'], $identity->getPid());
        $this->assertEquals($data['userType'], $identity->getUserType());
        $this->assertEquals($data['loginId'], $identity->getUsername());
        $this->assertEquals($data, $identity->getUserData());
        $this->assertEquals(['role1', 'role2'], $identity->getRoles());

        // test the backend is called only once for any following getIdentity() calls
        $this->assertEquals($identity, $this->sut->getIdentity());
    }

    public function testGetIdentityThrowsUnableToRetrieveException()
    {
        $mockResponse = m::mock();
        $mockResponse->shouldReceive('getResult')->with()->once()->andReturn(['messages' => ['foo', 'bar']]);
        $mockResponse->shouldReceive('isOk')->andReturn(false);

        $this->queryService->shouldReceive('send')->once()->andReturn($mockResponse);

        $this->setExpectedException(\Exception::class, 'Unable to retrieve identity - foo; bar');
        $this->sut->getIdentity();
    }
}
