<?php
namespace Common\Service\User;

use Common\Service\Cqrs\Command\CommandSender;
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

    public function __construct()
    {
        $this->commandSender = $this->getServiceLocator()->get('CommandSender');
    }

    public function updateLastLogin($userId)
    {
        $command = UpdateUserLastLoginAt::create([
            'id' => $userId
        ]);

        $this->commandSender->send($command);
    }
}