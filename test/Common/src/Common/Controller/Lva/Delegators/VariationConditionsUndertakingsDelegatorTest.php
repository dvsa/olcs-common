<?php

namespace CommonTest\Controller\Lva\Delegators;

use Common\Controller\Lva\Delegators\VariationConditionsUndertakingsDelegator;

class VariationConditionsUndertakingsDelegatorTest extends AdapterDelegatorTestAbstract
{
    protected $delegator = VariationConditionsUndertakingsDelegator::class;

    protected $adapter = 'VariationConditionsUndertakingsAdapter';
}
