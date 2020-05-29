<?php
namespace Common\Service\User;

use Common\Service\Cqrs\Command\CommandSender;
use Common\Service\Cqrs\Exception;
use Dvsa\Olcs\Transfer\Command\User\UpdateUserLastLoginAt;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

final class LastLoginService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var CommandSender
     */
    private $commandSender;

    public function __construct(CommandSender $commandSender)
    {
        $this->commandSender = $commandSender;
    }

    /**
     * @param string $username
     * @param string $token
     */
    public function updateLastLogin(string $token)
    {
        $command = UpdateUserLastLoginAt::create([
            'secureToken' => $token
        ]);

        $response = $this->commandSender->send($command);

        if (!$response->isOk()) {
            //TODO: Replace with exception!
            var_dump($response);
            die($response);
        }
    }
}