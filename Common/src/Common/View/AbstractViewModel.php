<?php

/**
 * Abstract View Model
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\View;

use Common\Service\Helper\UrlHelperService;
use Common\Service\Table\TableFactory;
use Laminas\View\Model\ViewModel;

/**
 * Abstract View Model
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractViewModel extends ViewModel
{
    /**
     * Build a table from config and results, and return the table object
     *
     * @param string $table
     * @param array $results
     * @param UrlHelperService $urlHelper
     * @param TableFactory $tableService
     * @param array $data
     */
    public function getTable(
        string $table,
        array $results,
        UrlHelperService $urlHelper,
        TableFactory $tableService,
        array $data = array())
    {
        if (!isset($data['url'])) {
            $data['url'] = $urlHelper;
        }

        return $tableService->buildTable($table, $results, $data, false);
    }
}
