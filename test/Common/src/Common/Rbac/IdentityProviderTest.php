<?php

namespace CommonTest\Rbac;

use Common\Rbac\User;
use Common\Rbac\IdentityProvider;
use Common\Service\Cqrs\Query\QuerySender;
use Common\Service\Cqrs\Response;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Laminas\Http\Header\GenericHeader;
use Laminas\Http\Request;
use Laminas\Session\Container;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;

/**
 * Class IdentityProviderTest
 * @package CommonTest\Rbac
 */
class IdentityProviderTest extends TestCase
{
    const USER_ID = 22;
    const HEADER_PID = '12345abc';

    public function testGetIdentityFromCache()
    {
        $cookie = [
            'secureToken' => 'secure',
        ];

        $cacheDataUserType = 'cache data user type';
        $cacheDataLoginId = 'cache data user type';

        $cacheData = [
            'userType' => $cacheDataUserType,
            'loginId' => $cacheDataLoginId,
        ];

        $identity = m::mock(User::class);
        $identity->expects('getId')->times(2)->andReturn(self::USER_ID);
        $identity->expects('getPid')->andReturn(self::HEADER_PID);
        $identity->expects('setUserType')->with($cacheDataUserType);
        $identity->expects('setUsername')->with($cacheDataLoginId);
        $identity->expects('setUserData')->with($cacheData);

        $queryService = m::mock(QuerySender::class);
        $session = m::mock(Container::class);
        $session->expects('offsetGet')->with('identity')->andReturn($identity);
        $session->expects('offsetSet')->with('identity', $identity);

        $request = m::mock(Request::class);
        $request->expects('getCookie')->andReturn($cookie);
        $request->expects('getHeader')
            ->with('X-Pid', m::type(GenericHeader::class))
            ->andReturn(
                m::mock(GenericHeader::class)
                    ->shouldReceive('getFieldValue')
                    ->andReturn(self::HEADER_PID)
                    ->getMock()
            );

        $cache = m::mock(CacheEncryption::class);
        $cache->expects('hasCustomItem')
            ->with(CacheEncryption::USER_ACCOUNT_IDENTIFIER, self::USER_ID)
            ->andReturnTrue();
        $cache->expects('getCustomItem')
            ->with(CacheEncryption::USER_ACCOUNT_IDENTIFIER, self::USER_ID)
            ->andReturn($cacheData);

        $sut = new IdentityProvider($queryService, $session, $request, $cache);

        $this->assertEquals($identity, $sut->getIdentity());
    }

    /**
     * @dataProvider dpGetIdentityWithDbUpdate
     */
    public function testGetIdentityWithDbUpdate($identity, $headerPid, $requestChecked, $cacheChecked)
    {
        $cookie = [
            'secureToken' => 'secure',
        ];

        $queryService = m::mock(QuerySender::class);
        $session = m::mock(Container::class);
        $session->expects('offsetGet')->with('identity')->andReturn($identity);
        $session->expects('offsetSet')->with('identity', m::type(User::class));

        $request = m::mock(Request::class);
        $request->expects('getCookie')->times($requestChecked)->andReturn($cookie);
        $request->expects('getHeader')
            ->times($requestChecked)
            ->with('X-Pid', m::type(GenericHeader::class))
            ->andReturn(
                m::mock(GenericHeader::class)
                    ->shouldReceive('getFieldValue')
                    ->andReturn($headerPid)
                    ->getMock()
            );

        $cache = m::mock(CacheEncryption::class);
        $cache->expects('hasCustomItem')
            ->with(CacheEncryption::USER_ACCOUNT_IDENTIFIER, self::USER_ID)
            ->times($cacheChecked)
            ->andReturnFalse();

        $sut = new IdentityProvider($queryService, $session, $request, $cache);

        $data = [
            'id' => self::USER_ID,
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

        $queryService
            ->shouldReceive('setRecoverHttpClientException')
            ->once()
            ->with(true)
            ->shouldReceive('send')
            ->once()
            ->andReturn($mockResponse);

        $identity = $sut->getIdentity();
        $this->assertInstanceOf(User::class, $identity);
        $this->assertEquals($data['id'], $identity->getId());
        $this->assertEquals($data['pid'], $identity->getPid());
        $this->assertEquals($data['userType'], $identity->getUserType());
        $this->assertEquals($data['loginId'], $identity->getUsername());
        $this->assertEquals($data, $identity->getUserData());
        $this->assertEquals(['role1', 'role2'], $identity->getRoles());

        // test the backend is called only once for any following getIdentity() calls
        $this->assertEquals($identity, $sut->getIdentity());
    }

    public function dpGetIdentityWithDbUpdate()
    {
        $emptyUser = new User();

        $rbacUserEmptyPid = new User();
        $rbacUserEmptyPid->setId(self::USER_ID);

        $rbacUserWithPid = new User();
        $rbacUserWithPid->setId(self::USER_ID);
        $rbacUserWithPid->setPid(self::HEADER_PID);

        return [
            [null, null, 0, 0], //no identity, not authenticated
            [null, self::HEADER_PID, 0, 0], //no identity, header pid included but not used
            [$emptyUser, null, 0, 0], //empty user identity, no header pid
            [$emptyUser, self::HEADER_PID, 0, 0], //empty user identity, header pid included but not used
            [$rbacUserEmptyPid, null, 1, 0], //user has id but no pid, no header pid (request checked)
            [$rbacUserEmptyPid, self::HEADER_PID, 1, 0], //user has id but no pid, header pid exists (request checked)
            [$rbacUserWithPid, null, 1, 0], //user has id and pid, but no header pid (request checked)
            [$rbacUserWithPid, 'zzzzz', 1, 0], //user has id and pid, but header pid doesn't match (request checked)
            [$rbacUserWithPid, self::HEADER_PID, 1, 1], //user and pid match (request anc cache both checked)
        ];
    }

    public function testGetIdentitySetNotIdentifiedUser()
    {
        $queryService = m::mock(QuerySender::class);
        $request = m::mock(Request::class);
        $cache = m::mock(CacheEncryption::class);

        $session = m::mock(Container::class);
        $session->expects('offsetGet')->with('identity')->andReturn(null);
        $session->expects('offsetSet')->with('identity', m::type(User::class));

        $sut = new IdentityProvider($queryService, $session, $request, $cache);

        $response = [
            'id' => null,
            'pid' => null,
            'userType' => User::USER_TYPE_NOT_IDENTIFIED,
            'loginId' => null,
            'roles' => []
        ];

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('getResult')->with()->once()->andReturn($response);
        $mockResponse->shouldReceive('isOk')->andReturn(false);
        $mockResponse->shouldReceive('setResult')->with($response);

        $queryService
            ->shouldReceive('setRecoverHttpClientException')
            ->once()
            ->with(true)
            ->shouldReceive('send')
            ->once()
            ->andReturn($mockResponse);

        /**  @var \Common\Rbac\User $identity */
        $identity = $sut->getIdentity();

        $this->assertInstanceOf(User::class, $identity);
        $this->assertNull($identity->getId());
        $this->assertNull($identity->getPid());
        $this->assertEquals(User::USER_TYPE_NOT_IDENTIFIED, $identity->getUserType());
        $this->assertNull($identity->getUsername());
        $this->assertEquals([], $identity->getRoles());
    }
}
