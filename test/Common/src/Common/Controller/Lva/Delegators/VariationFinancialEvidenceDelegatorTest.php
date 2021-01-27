<?php

namespace CommonTest\Controller\Lva\Delegators;

use Common\Controller\Lva\Delegators\VariationFinancialEvidenceDelegator;

class VariationFinancialEvidenceDelegatorTest extends AdapterDelegatorTestAbstract
{
    protected $delegator = VariationFinancialEvidenceDelegator::class;

    protected $adapter = 'VariationFinancialEvidenceAdapter';
}
