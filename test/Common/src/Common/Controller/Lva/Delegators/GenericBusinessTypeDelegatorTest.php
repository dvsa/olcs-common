<?php

namespace CommonTest\Controller\Lva\Delegators;

use Common\Controller\Lva\Delegators\GenericBusinessTypeDelegator;

class GenericBusinessTypeDelegatorTest extends AdapterDelegatorTestAbstract
{
    protected $delegator = GenericBusinessTypeDelegator::class;

    protected $adapter = 'GenericBusinessTypeAdapter';
}
