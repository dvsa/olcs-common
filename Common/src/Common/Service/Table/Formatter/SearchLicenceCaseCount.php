<?php

namespace Common\Service\Table\Formatter;

use Common\RefData;
use Common\Util\Escape;
use ZfcRbac\Service\AuthorizationService;

/**
 *
 * @package Common\Service\Table\Formatter
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class SearchLicenceCaseCount implements FormatterPluginManagerInterface
{
    private AuthorizationService $authService;

    public function __construct(AuthorizationService $authService)
    {
        $this->authService = $authService;
    }

    /**
     *
     * @param array $data The row data.
     * @param array $column The column data.
     *
     * @return string
     */
    public function format($data, $column = [])
    {
        if ($this->authService->isGranted(RefData::PERMISSION_INTERNAL_IRHP_ADMIN)) {
            return Escape::html($data['caseCount']);
        }

        return '<a class="govuk-link" href="/licence/' . $data['licId'] . '/cases">' . Escape::html($data['caseCount']) . '</a>';
    }
}
