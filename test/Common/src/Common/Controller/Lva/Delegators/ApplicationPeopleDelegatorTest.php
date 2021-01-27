<?php

namespace CommonTest\Controller\Lva\Delegators;

use Common\Controller\Lva\Delegators\ApplicationPeopleDelegator;

class ApplicationPeopleDelegatorTest extends AdapterDelegatorTestAbstract
{
    protected $delegator = ApplicationPeopleDelegator::class;

    protected $adapter = 'ApplicationPeopleAdapter';
}
