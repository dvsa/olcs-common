<?php

namespace CommonTest\Controller\Lva\Delegators;

use Common\Controller\Lva\Delegators\ApplicationFinancialEvidenceDelegator;

class ApplicationFinancialEvidenceDelegatorTest extends AdapterDelegatorTestAbstract
{
    protected $delegator = ApplicationFinancialEvidenceDelegator::class;

    protected $adapter = 'ApplicationFinancialEvidenceAdapter';
}
