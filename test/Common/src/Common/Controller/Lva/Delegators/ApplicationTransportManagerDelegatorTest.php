<?php

namespace CommonTest\Controller\Lva\Delegators;

use Common\Controller\Lva\Delegators\ApplicationTransportManagerDelegator;

class ApplicationTransportManagerDelegatorTest extends AdapterDelegatorTestAbstract
{
    protected $delegator = ApplicationTransportManagerDelegator::class;

    protected $adapter = 'ApplicationTransportManagerAdapter';
}
