<?php
declare(strict_types=1);

namespace CommonTest\Rbac;

use Common\Auth\Service\RefreshTokenService;
use Common\Rbac\JWTIdentityProvider;
use Common\Rbac\User;
use Common\Service\Cqrs\Query\QuerySender;
use Common\Service\Cqrs\Response;
use Common\Test\MocksServicesTrait;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Laminas\Http\Response as HttpResponse;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Session\Container;
use Mockery\MockInterface;
use Olcs\TestHelpers\MockeryTestCase;

class JWTIdentityProviderTest extends MockeryTestCase
{
    use MocksServicesTrait;

    const DATA_WITH_ROLES = [
        'userType' => 'user_type',
        'loginId' => 'login_id',
        'id' => 1,
        'roles' => [
            ['role' => 'role1'],
            ['role' => 'role2'],
            ['role' => 'role3'],
        ]
    ];
    const DATA_WITHOUT_ROLES = [
        'userType' => 'user_type',
        'loginId' => 'login_id',
        'id' => 1
    ];

    /**
     * @var JWTIdentityProvider
     */
    protected $sut;

    /**
     * @test
     */
    public function getIdentity_ShouldRetriveDataFromCache_WhenShouldntUpdateAndCacheExists()
    {
        $this->setupSut();

        $session = $this->session();
        $session->allows('offsetGet')->with('identity')->andReturnUsing(function () {
            $user = new User();
            $user->setId(1);
            return $user;
        });

        $cacheService = $this->cacheService();
        $cacheService->allows('hasCustomItem')->andReturnTrue();

        $cacheService->expects('getCustomItem')->andReturn(static::DATA_WITHOUT_ROLES)->once();

        // Execute
        $this->sut->getIdentity();
    }

    /**
     * @test
     */
    public function getIdentity_ShouldRetriveDataFromDB_WhenIdentityIsNotInstanceOfUser()
    {
        // Setup
        $this->setupSut();

        $session = $this->session();
        $session->allows('offsetGet')->with('identity')->andReturnNull();

        // Expectations
        $querySender = $this->querySender();
        $querySender->expects('send')->once()->andReturn($this->response());

        // Execute
        $this->sut->getIdentity();
    }

    /**
     * @test
     */
    public function getIdentity_ShouldRetriveDataFromDB_WhenIdentityHasNoId()
    {
        // Setup
        $this->setupSut();

        $session = $this->session();
        $session->allows('offsetGet')->with('identity')->andReturn(new User());

        // Expectations
        $querySender = $this->querySender();
        $querySender->expects('send')->once()->andReturn($this->response());

        // Execute
        $this->sut->getIdentity();
    }

    /**
     * @test
     */
    public function getIdentity_ShouldRetriveDataFromDB_WhenCacheDoesntExist()
    {
        // Setup
        $this->setupSut();

        $session = $this->session();
        $session->allows('offsetGet')->with('identity')->andReturnUsing(function () {
            $user = new User();
            $user->setId(1);
            return $user;
        });

        $cacheService = $this->cacheService();
        $cacheService->allows('hasCustomItem')->andReturnFalse();

        // Expectations
        $querySender = $this->querySender();
        $querySender->expects('send')->once()->andReturn($this->response());

        // Execute
        $this->sut->getIdentity();
    }

    /**
     * @test
     */
    public function getIdentity_ShouldStoreIdentityInTheSession()
    {
        // Setup
        $this->setupSut();

        // Expectations
        $session = $this->session();
        $session->expects('offsetSet')->withSomeOfArgs('identity')->once();

        // Execute
        $this->sut->getIdentity();
    }

    /**
     * @test
     */
    public function getIdentity_ShouldReturnInstanceofUser()
    {
        // Setup
        $this->setupSut();

        // Execute
        $identity = $this->sut->getIdentity();

        // Assertions
        $this->assertInstanceOf(User::class, $identity);
    }

    /**
     * @test
     */
    public function getIdentity_ShouldReturnInstanceofUser_WithRoles()
    {
        // Setup
        $this->setupSut();

        $querySender = $this->querySender();
        $querySender->expects('send')->andReturn($this->response(true, static::DATA_WITH_ROLES));

        // Execute
        $identity = $this->sut->getIdentity();

        // Assertions
        $this->assertInstanceOf(User::class, $identity);
        $this->assertSame(['role1', 'role2', 'role3'], $identity->getRoles());
    }

    /**
     * @test
     */
    public function getIdentity_ShouldRefreshTokens_WhenRequired_AndCacheExists()
    {
        // Setup
        $this->setupSut();

        $session = $this->session();
        $session->allows('offsetGet')->with('identity')->andReturnUsing(function () {
            $user = new User();
            $user->setId(1);
            $user->setUsername('username');
            return $user;
        });
        $session->allows('offsetGet')->with('storage')->andReturn(['Token' => ['refreshToken' => 'abc1234']]);

        $cacheService = $this->cacheService();
        $cacheService->allows('hasCustomItem')->andReturnTrue();

        $cacheService->expects('getCustomItem')->andReturn(static::DATA_WITHOUT_ROLES)->once();

        // Expectations
        $refreshService = $this->refreshTokenService();
        $refreshService->expects('isRefreshRequired')->andReturnTrue();

        $session->expects('offsetSet')->with('storage', []);

        // Execute
        $this->sut->getIdentity();
    }

    /**
     * @test
     */
    public function getIdentity_ShouldRefreshTokens_WhenRequired_AndCacheDoesntExist()
    {
        // Setup
        $this->setupSut();

        $session = $this->session();
        $session->allows('offsetGet')->with('identity')->andReturnUsing(function () {
            $user = new User();
            $user->setId(1);
            $user->setUsername('username');
            return $user;
        });
        $session->allows('offsetGet')->with('storage')->andReturn(['Token' => ['refreshToken' => 'abc1234']]);

        $cacheService = $this->cacheService();
        $cacheService->allows('hasCustomItem')->andReturnFalse();

        // Expectations
        $refreshService = $this->refreshTokenService();
        $refreshService->expects('isRefreshRequired')->andReturnTrue();

        $session->expects('offsetSet')->with('storage', []);

        // Execute
        $this->sut->getIdentity();
    }

    /**
     * @test
     */
    public function getIdentity_ShouldReturnExistingIdentity_WhenPresent()
    {
        // Setup
        $this->setupSut();

        // Expectations

        $session = $this->session();
        $session->expects('offsetGet')->with('identity')->once();

        // Execute
        $this->sut->getIdentity();
        $this->sut->getIdentity();
    }

    public function setUp(): void
    {
        $this->setUpServiceManager();
    }

    protected function setupSut()
    {
        $this->sut = new JWTIdentityProvider(
            $this->session(),
            $this->querySender(),
            $this->cacheService(),
            $this->refreshTokenService()
        );
    }

    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $this->cacheService();
        $this->querySender();
        $this->session();
    }

    /**
     * @return MockInterface|CacheEncryption
     */
    private function cacheService()
    {
        if (!$this->serviceManager->has(CacheEncryption::class)) {
            $instance = $this->setUpMockService(CacheEncryption::class);
            $this->serviceManager->setService(CacheEncryption::class, $instance);
        }
        $instance = $this->serviceManager->get(CacheEncryption::class);
        return $instance;
    }

    /**
     * @return MockInterface|QuerySender
     */
    private function querySender()
    {
        if (!$this->serviceManager->has(QuerySender::class)) {
            $instance = $this->setUpMockService(QuerySender::class);
            $instance->allows('send')->andReturn($this->response())->byDefault();
            $this->serviceManager->setService(QuerySender::class, $instance);
        }
        $instance = $this->serviceManager->get(QuerySender::class);
        return $instance;
    }

    /**
     * @return MockInterface|Container
     */
    private function session()
    {
        if (!$this->serviceManager->has(Container::class)) {
            $instance = $this->setUpMockService(Container::class);
            $this->serviceManager->setService(Container::class, $instance);
        }
        $instance = $this->serviceManager->get(Container::class);
        return $instance;
    }

    /**
     * @return MockInterface|RefreshTokenService
     */
    protected function refreshTokenService()
    {
        if (!$this->serviceManager->has(RefreshTokenService::class)) {
            $instance = $this->setUpMockService(RefreshTokenService::class);
            $this->serviceManager->setService(RefreshTokenService::class, $instance);
        }
        return $this->serviceManager->get(RefreshTokenService::class);
    }

    private function response(bool $isSuccess = false, array $result = []): Response
    {
        $httpResponse = new HttpResponse();
        $httpResponse->setStatusCode($isSuccess ? 200:500);

        $response = new Response($httpResponse);
        $response->setResult($result);

        return $response;
    }
}
