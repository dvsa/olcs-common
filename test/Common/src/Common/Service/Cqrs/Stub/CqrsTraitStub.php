<?php

namespace CommonTest\Common\Service\Cqrs\Stub;

use Common\Service\Cqrs\CqrsTrait;

class CqrsTraitStub
{
    use CqrsTrait;

    public function testShowApiMessagesFromResponse($response): void
    {
        $this->showApiMessagesFromResponse($response);
    }

    public function setFlashMessenger($msngr): void
    {
        $this->flashMessenger = $msngr;
    }
}
