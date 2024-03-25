<?php

namespace Common\Service\User;

use Common\Service\Cqrs\Command\CommandSender;
use Common\Service\Cqrs\Response;
use Dvsa\Olcs\Transfer\Command\User\UpdateUserLastLoginAt;

class LastLoginService
{
    /**
     * @var CommandSender
     */
    private $commandSender;

    public function __construct(CommandSender $commandSender)
    {
        $this->commandSender = $commandSender;
    }

    /**
     * @return Response
     */
    public function updateLastLogin(string $token)
    {
        $command = UpdateUserLastLoginAt::create([
            'secureToken' => $token
        ]);

        return $this->commandSender->send($command);
    }
}
