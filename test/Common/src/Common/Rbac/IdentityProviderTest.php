<?php

namespace CommonTest\Rbac;

use Common\Rbac\User;
use Common\Rbac\IdentityProvider;
use Common\Service\Cqrs\Query\QuerySender;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Zend\Http\Header\GenericHeader;
use Zend\Http\Request;
use Zend\Session\Container;

/**
 * Class IdentityProviderTest
 * @package CommonTest\Rbac
 */
class IdentityProviderTest extends TestCase
{
    private $sut;

    private $queryService;

    private $session;

    private $request;

    public function setUp()
    {
        $this->queryService = m::mock(QuerySender::class);
        $this->session = m::mock(Container::class);
        $this->request = m::mock(Request::class);

        $this->sut = new IdentityProvider($this->queryService, $this->session, $this->request);
    }

    public function testGetIdentity()
    {
        $this->session->shouldReceive('offsetExists')->with('identity')->andReturn(false);

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

        $this->session->shouldReceive('offsetSet')
            ->once()
            ->with('identity', m::type(User::class))
            ->andReturnUsing(
                function ($key, User $user) use ($data) {
                    $this->assertEquals($data['id'], $user->getId());
                    $this->assertEquals($data['pid'], $user->getPid());
                    $this->assertEquals($data['userType'], $user->getUserType());
                    $this->assertEquals($data['loginId'], $user->getUsername());
                    $this->assertEquals($data, $user->getUserData());
                    $this->assertEquals(['role1', 'role2'], $user->getRoles());
                }
            );

        $this->session->shouldReceive('offsetGet')->with('identity')->once()->andReturn('SESSION_USER');

        $this->assertEquals('SESSION_USER', $this->sut->getIdentity());
    }

    public function testGetIdentityWithSessionEmpty()
    {
        $this->session->shouldReceive('offsetExists')->with('identity')->andReturn(true);
        $this->session->shouldReceive('offsetGet')->with('identity')->andReturn(null, 'SESSION_USER');

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

        $this->session->shouldReceive('offsetSet')
            ->once()
            ->with('identity', m::type(User::class));

        $this->assertEquals('SESSION_USER', $this->sut->getIdentity());
    }

    public function testGetIdentityWithAuthenticatedUserAndPidMismatch()
    {
        $sessionUser = new User();
        $sessionUser->setPid('12345');

        $this->session->shouldReceive('offsetExists')->with('identity')->andReturn(true);
        $this->session->shouldReceive('offsetGet')->with('identity')->andReturn($sessionUser, 'SESSION_USER');

        $cookies = ['secureToken' => 'secure-token'];
        $this->request->shouldReceive('getCookie')->andReturn($cookies);
        $this->request->shouldReceive('getHeader')
            ->with('X-Pid', m::type(GenericHeader::class))
            ->andReturn(
                m::mock()
                    ->shouldReceive('getFieldValue')
                    ->andReturn('67890')
                    ->getMock()
            );

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

        $this->session->shouldReceive('offsetSet')
            ->once()
            ->with('identity', m::type(User::class));

        $this->assertEquals('SESSION_USER', $this->sut->getIdentity());
    }

    public function testGetIdentityWithAuthenticatedUserAndMatchedPid()
    {
        $sessionUser = new User();
        $sessionUser->setPid('12345');

        $this->session->shouldReceive('offsetExists')->with('identity')->andReturn(true);
        $this->session->shouldReceive('offsetGet')->with('identity')->andReturn($sessionUser, 'SESSION_USER');

        $cookies = ['secureToken' => 'secure-token'];
        $this->request->shouldReceive('getCookie')->andReturn($cookies);
        $this->request->shouldReceive('getHeader')
            ->with('X-Pid', m::type(GenericHeader::class))
            ->andReturn(
                m::mock()
                    ->shouldReceive('getFieldValue')
                    ->andReturn('12345')
                    ->getMock()
            );

        $this->queryService->shouldReceive('send')->never();

        $this->assertEquals('SESSION_USER', $this->sut->getIdentity());
    }

    public function testGetIdentityWithNotAuthenticatedUserAndSessionNotAnonymous()
    {
        $sessionUser = new User();
        $sessionUser->setPid('12345');
        $sessionUser->setUserType(User::USER_TYPE_OPERATOR);

        $this->session->shouldReceive('offsetExists')->with('identity')->andReturn(true);
        $this->session->shouldReceive('offsetGet')->with('identity')->andReturn($sessionUser, 'SESSION_USER');

        $cookies = ['secureToken' => ''];
        $this->request->shouldReceive('getCookie')->andReturn($cookies);
        $this->request->shouldReceive('getHeader')
            ->with('X-Pid', m::type(GenericHeader::class))
            ->andReturn(
                m::mock()
                    ->shouldReceive('getFieldValue')
                    ->andReturn('')
                    ->getMock()
            );

        $data = [
            'id' => '',
            'pid' => '',
            'userType' => User::USER_TYPE_ANON,
            'loginId' => 'anon',
            'roles' => []
        ];

        $mockResponse = m::mock();
        $mockResponse->shouldReceive('isOk')->andReturn(true);
        $mockResponse->shouldReceive('getResult')->andReturn($data);

        $this->queryService->shouldReceive('send')->once()->andReturn($mockResponse);

        $this->session->shouldReceive('offsetSet')
            ->once()
            ->with('identity', m::type(User::class));

        $this->assertEquals('SESSION_USER', $this->sut->getIdentity());
    }

    public function testGetIdentityWithNotAuthenticatedUserAndAnonymousSession()
    {
        $sessionUser = new User();
        $sessionUser->setUserType(User::USER_TYPE_ANON);

        $this->session->shouldReceive('offsetExists')->with('identity')->andReturn(true);
        $this->session->shouldReceive('offsetGet')->with('identity')->andReturn($sessionUser, 'SESSION_USER');

        $cookies = ['secureToken' => ''];
        $this->request->shouldReceive('getCookie')->andReturn($cookies);
        $this->request->shouldReceive('getHeader')
            ->with('X-Pid', m::type(GenericHeader::class))
            ->andReturn(
                m::mock()
                    ->shouldReceive('getFieldValue')
                    ->andReturn('')
                    ->getMock()
            );

        $this->queryService->shouldReceive('send')->never();

        $this->assertEquals('SESSION_USER', $this->sut->getIdentity());
    }

    public function testGetIdentityThrowsUnableToRetrieveException()
    {
        $this->setExpectedException('Exception');

        $mockResponse = m::mock();
        $mockResponse->shouldReceive('isOk')->andReturn(false);

        $this->queryService->shouldReceive('send')->once()->andReturn($mockResponse);

        $this->session->shouldReceive('offsetExists')->with('identity')->andReturn(false);

        $this->sut->getIdentity();
    }
}
