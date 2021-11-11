<?php
declare(strict_types=1);

namespace CommonTest\Auth\Adapter;

use Closure;
use Common\Auth\Listener\RefreshJWTListener;
use Common\Service\Cqrs\Command\CommandSender;
use Common\Service\Cqrs\Response;
use Laminas\Console\Request as ConsoleRequest;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Http\Request as HttpRequest;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;
use Mockery as m;
use Mockery\MockInterface;
use Olcs\TestHelpers\MockeryTestCase;

/**
 * Class CommandAdapterTest
 * @see CommandAdapter
 */
class RefreshJWTListenerTest extends MockeryTestCase
{
    /**
     * @var RefreshJWTListener
     */
    protected RefreshJWTListener $sut;

    /**
     * @test
     */
    public function attach_AttachesToDispatchEvent()
    {
        // Setup
        $commandSender = $this->commandSender();
        $session = $this->session();
        $this->sut = $this->setupSut($commandSender, $session);

        // Expectations
        $events = m::mock(EventManagerInterface::class);
        $events->shouldReceive('attach')
            ->withSomeOfArgs(
                MvcEvent::EVENT_DISPATCH,
            );

        $this->sut->attach($events);
    }

    /**
     * @test
     */
    public function onDispatch_DoesNotAct_OnNonHttpRequestEvent()
    {
        // Setup
        $commandSender = $this->commandSender();
        $session = $this->session();
        $this->sut = $this->setupSut($commandSender, $session);
        $event = $this->event(ConsoleRequest::class);

        // Expectations
        $commandSender->shouldNotHaveBeenCalled();
        $session->shouldNotHaveBeenCalled();

        // Execute
        $this->sut->onDispatch($event);
    }

    /**
     * @test
     */
    public function onDispatch_DoesNotAct_WhenTokenIsNotPresentInSession()
    {
        // Setup
        $commandSender = $this->commandSender();
        $session = $this->session();
        $this->sut = $this->setupSut($commandSender, $session);
        $event = $this->event(HttpRequest::class);

        // Expectations
        $session->shouldReceive('offsetGet')->andReturnNull();

        $commandSender->shouldNotHaveBeenCalled();

        // Execute
        $this->sut->onDispatch($event);
    }

    /**
     * @test
     */
    public function onDispatch_DoesNotAct_WhenTokenIsNotDueToExpire()
    {
        // Setup
        $commandSender = $this->commandSender();
        $session = $this->session();
        $this->sut = $this->setupSut($commandSender, $session);
        $event = $this->event(HttpRequest::class);

        // Expectations
        $session->shouldReceive('offsetGet')
            ->andReturn($this->sessionContents(120));

        $commandSender->shouldNotHaveBeenCalled();

        // Execute
        $this->sut->onDispatch($event);
    }

    /**
     * @test
     */
    public function onDispatch_ThrowsException_WhenResultIsNotOk()
    {
        // Setup
        $commandSender = $this->commandSender();
        $session = $this->session();
        $this->sut = $this->setupSut($commandSender, $session);
        $event = $this->event(HttpRequest::class);

        // Expectations
        $session->shouldReceive('offsetGet')
            ->andReturn($this->sessionContents(15));

        $commandSender->shouldReceive('send')
            ->andReturn($this->response(false, []));

        $this->expectException(\Exception::class);
        $this->expectErrorMessage(sprintf(RefreshJWTListener::MESSAGE_BASE, RefreshJWTListener::MESSAGE_RESULT_NOT_OK));

        // Execute
        $this->sut->onDispatch($event);
    }

    /**
     * @test
     */
    public function onDispatch_ThrowsException_WhenResultIsNotValid()
    {
        // Setup
        $commandSender = $this->commandSender();
        $session = $this->session();
        $this->sut = $this->setupSut($commandSender, $session);
        $event = $this->event(HttpRequest::class);

        // Expectations
        $session->shouldReceive('offsetGet')
            ->andReturn($this->sessionContents(15));

        $result = ['flags' => ['isValid' => false]];
        $commandSender->shouldReceive('send')
            ->andReturn($this->response(true, $result));

        $this->expectException(\Exception::class);
        $this->expectErrorMessage(
            sprintf(RefreshJWTListener::MESSAGE_BASE, RefreshJWTListener::MESSAGE_AUTH_RESULT_NOT_VALID)
        );

        // Execute
        $this->sut->onDispatch($event);
    }

    /**
     * @test
     */
    public function onDispatch_ThrowsException_WhenResultIsMissingIdentity()
    {
        // Setup
        $commandSender = $this->commandSender();
        $session = $this->session();
        $this->sut = $this->setupSut($commandSender, $session);
        $event = $this->event(HttpRequest::class);

        // Expectations
        $session->shouldReceive('offsetGet')
            ->andReturn($this->sessionContents(15));

        $result = ['flags' => ['isValid' => true]];
        $commandSender->shouldReceive('send')
            ->andReturn($this->response(true, $result));

        $this->expectException(\Exception::class);
        $this->expectErrorMessage(
            sprintf(RefreshJWTListener::MESSAGE_BASE, RefreshJWTListener::MESSAGE_IDENTITY_MISSING)
        );

        // Execute
        $this->sut->onDispatch($event);
    }

    /**
     * @test
     */
    public function onDispatch_StoresNewIdentity()
    {
        // Setup
        $commandSender = $this->commandSender();
        $session = $this->session();
        $this->sut = $this->setupSut($commandSender, $session);
        $event = $this->event(HttpRequest::class);

        // Expectations
        $session->shouldReceive('offsetGet')
            ->andReturn($this->sessionContents(15));
        $session->shouldReceive('offsetSet')
            ->with('storage', ['provider' => 'mock']);

        $result = ['flags' => ['isValid' => true, 'identity' => ['provider' => 'mock']]];
        $commandSender->shouldReceive('send')
            ->andReturn($this->response(true, $result));

        // Execute
        $this->sut->onDispatch($event);
    }

    /**
     * @param bool $isOk
     * @param array $result
     * @return Response|MockInterface
     */
    protected function response(bool $isOk, array $result)
    {
        $instance = m::mock(Response::class);
        $instance->shouldReceive('isOk')
            ->andReturn($isOk);
        $instance->shouldReceive('getResult')
            ->andReturn($result);

        return $instance;
    }

    /**
     * @param Response|null $response
     * @return CommandSender|MockInterface
     */
    protected function commandSender(?Response $response = null)
    {
        $instance = m::mock(CommandSender::class);
        $instance->allows('send')
            ->andReturn($response)
            ->byDefault();

        return $instance;
    }

    /**
     * @return Container|MockInterface
     */
    protected function session()
    {
        return m::mock(Container::class);
    }

    /**
     * @param string $requestClass
     * @return MvcEvent|MockInterface
     */
    protected function event(string $requestClass)
    {
        $request = m::mock($requestClass);

        $instance = m::mock(MvcEvent::class);
        $instance->shouldReceive('getRequest')
            ->andReturn($request);

        return $instance;
    }

    /**
     * @param CommandSender $commandSender
     * @param Container $session
     * @return RefreshJWTListener
     */
    protected function setupSut(CommandSender $commandSender, Container $session): RefreshJWTListener
    {
        return new RefreshJWTListener($commandSender, $session);
    }

    /**
     * @return array
     */
    private function sessionContents(int $expireIn): array
    {
        return [
            'Token' => [
                'expires' => (time() + $expireIn),
                'refresh_token' => 'abc1235chcdewjfdewj'
            ]
        ];
    }
}
