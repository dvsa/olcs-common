<?php

namespace Common\Service\Table\Formatter;

use Common\RefData;
use Common\Util\Escape;
use LmcRbacMvc\Service\AuthorizationService;

/**
 *
 * @package Common\Service\Table\Formatter
 *
*
 */
class SearchCasesCaseId implements FormatterPluginManagerInterface
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
            return Escape::html($data['caseId']);
        }

        return '<a class="govuk-link" href="/case/details/' . $data['caseId'] . '">' . Escape::html($data['caseId']) . '</a>';
    }
}
