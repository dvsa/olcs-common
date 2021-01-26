<?php

namespace CommonTest\Controller\Lva\Delegators;

use Common\Controller\Lva\Delegators\VariationTransportManagerDelegator;

class VariationTransportManagerDelegatorTest extends AdapterDelegatorTestAbstract
{
    protected $delegator = VariationTransportManagerDelegator::class;

    protected $adapter = 'VariationTransportManagerAdapter';
}
