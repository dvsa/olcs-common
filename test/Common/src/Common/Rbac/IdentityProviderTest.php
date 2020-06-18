<?php

namespace CommonTest\Rbac;

use Common\Rbac\User;
use Common\Rbac\IdentityProvider;
use Common\Service\Cqrs\Query\QuerySender;
use Common\Service\Cqrs\Response;
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

    public function setUp(): void
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

        $this->queryService
            ->shouldReceive('setRecoverHttpClientException')
            ->once()
            ->with(true)
            ->shouldReceive('send')
            ->once()
            ->andReturn($mockResponse);

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

    public function testGetIdentitySetNotIdentifiedUser()
    {

        $response = [
            'id' => null,
            'pid' => null,
            'userType' => User::USER_TYPE_NOT_IDENTIFIED,
            'loginId' => null,
            'roles' => []
        ];

        $mockResponse = m::mock();
        $mockResponse->shouldReceive('getResult')->with()->once()->andReturn($response);
        $mockResponse->shouldReceive('isOk')->andReturn(false);
        $mockResponse->shouldReceive('setResult')->with($response);

        $this->queryService
            ->shouldReceive('setRecoverHttpClientException')
            ->once()
            ->with(true)
            ->shouldReceive('send')
            ->once()
            ->andReturn($mockResponse);

        /**  @var \Common\Rbac\User $identity */
        $identity = $this->sut->getIdentity();

        $this->assertInstanceOf(User::class, $identity);
        $this->assertNull($identity->getId());
        $this->assertNull($identity->getPid());
        $this->assertEquals(User::USER_TYPE_NOT_IDENTIFIED, $identity->getUserType());
        $this->assertNull($identity->getUsername());
        $this->assertEquals([], $identity->getRoles());
    }
}
