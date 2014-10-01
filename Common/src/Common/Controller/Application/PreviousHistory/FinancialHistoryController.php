<?php

/**
 * FinancialHistory Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Application\PreviousHistory;

use Common\Controller\Traits;

/**
 * FinancialHistory Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FinancialHistoryController extends PreviousHistoryController
{
    use Traits\GenericIndexAction;

    protected $sectionServiceName = 'PreviousHistory\\ExternalApplicationFinancialHistory';

}
