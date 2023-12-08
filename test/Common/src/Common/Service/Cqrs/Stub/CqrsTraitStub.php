<?php

namespace CommonTest\Common\Service\Cqrs\Stub;

use Common\Service\Cqrs\CqrsTrait;

class CqrsTraitStub
{
    use CqrsTrait;

    public function testShowApiMessagesFromResponse($response)
    {
        $this->showApiMessagesFromResponse($response);
    }

    public function setFlashMessenger($msngr)
    {
        $this->flashMessenger = $msngr;
    }
}
