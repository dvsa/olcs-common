<?php
declare(strict_types=1);

namespace Common\Auth\Listener;

use Common\Service\Cqrs\Command\CommandSender;
use Dvsa\Olcs\Transfer\Command\Auth\RefreshToken;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\Http\Request as HttpRequest;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;

class RefreshJWTListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    const MESSAGE_BASE = "JWT refresh failed: %s";
    const MESSAGE_RESULT_NOT_OK = 'Result is not ok';
    const MESSAGE_AUTH_RESULT_NOT_VALID = 'Result is not valid';
    const MESSAGE_IDENTITY_MISSING= 'Result is missing new identity';

    protected CommandSender $commandSender;

    protected Container $session;

    /**
     * RefreshJWTListener constructor.
     * @param CommandSender $commandSender
     * @param Container $session
     */
    public function __construct(CommandSender $commandSender, Container $session)
    {
        $this->commandSender = $commandSender;
        $this->session = $session;
    }


    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], $priority);
    }

    public function onDispatch(MvcEvent $e): void
    {
        $request = $e->getRequest();

        if (!($request instanceof HttpRequest)) {
            return;
        }

        $token = $this->session->offsetGet('storage')['Token'] ?? null;
        if (empty($token)) {
            return;
        }

        // Only refresh the token if it expires within the next minute
        if ($token['expires'] - time() > 60) {
            return;
        }

        $refreshCommand = RefreshToken::create(['refreshToken' => $token['refresh_token']]);
        $result = $this->commandSender->send($refreshCommand);

        if (!$result->isOk()) {
            throw new \Exception(sprintf(static::MESSAGE_BASE, static::MESSAGE_RESULT_NOT_OK));
        }

        $flags = $result->getResult()['flags'];
        if (!$flags['isValid']) {
            throw new \Exception(sprintf(static::MESSAGE_BASE, static::MESSAGE_AUTH_RESULT_NOT_VALID));
        }

        $identity = $flags['identity'] ?? null;
        if (empty($identity)) {
            throw new \Exception(sprintf(static::MESSAGE_BASE, static::MESSAGE_IDENTITY_MISSING));
        }

        $this->session->offsetSet('storage', $identity);
    }
}
