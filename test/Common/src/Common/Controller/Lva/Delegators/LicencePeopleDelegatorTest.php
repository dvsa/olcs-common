<?php

namespace CommonTest\Controller\Lva\Delegators;

use Common\Controller\Lva\Delegators\LicencePeopleDelegator;

class LicencePeopleDelegatorTest extends AdapterDelegatorTestAbstract
{
    protected $delegator = LicencePeopleDelegator::class;

    protected $adapter = 'LicencePeopleAdapter';
}
