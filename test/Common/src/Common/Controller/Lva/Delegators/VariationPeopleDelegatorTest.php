<?php

namespace CommonTest\Controller\Lva\Delegators;

use Common\Controller\Lva\Delegators\VariationPeopleDelegator;

class VariationPeopleDelegatorTest extends AdapterDelegatorTestAbstract
{
    protected $delegator = VariationPeopleDelegator::class;

    protected $adapter = 'VariationPeopleAdapter';
}
