<?php

namespace CommonTest\Controller\Lva\Delegators;

use Common\Controller\Lva\Delegators\LicenceTransportManagerDelegator;

class LicenceTransportManagerDelegatorTest extends AdapterDelegatorTestAbstract
{
    protected $delegator = LicenceTransportManagerDelegator::class;

    protected $adapter = 'LicenceTransportManagerAdapter';
}
