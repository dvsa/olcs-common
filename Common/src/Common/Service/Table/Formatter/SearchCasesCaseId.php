<?php

namespace Common\Service\Table\Formatter;

use Common\RefData;
use Common\Util\Escape;
use ZfcRbac\Service\AuthorizationService;

/**
 * Class AccessedCorrespondence
 *
 * Accessed correspondence formatter, displays correspondence as a link to the document and
 * denotes whether the correspondence has been accessed.
 *
 * @package Common\Service\Table\Formatter
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class SearchCasesCaseId implements FormatterPluginManagerInterface
{
    private AuthorizationService $authService;

    public function __construct(AuthorizationService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Get a link for the document with the access indicator.
     *
     * @param array $data The row data.
     * @param array $column The column data.
     *
     * @return string The document link and accessed indicator
     */
    public function format($data, $column = [])
    {
        if ($this->authService->isGranted(RefData::PERMISSION_INTERNAL_IRHP_ADMIN)) {
            return Escape::html($data['caseId']);
        }

        return '<a class="govuk-link" href="/case/details/' . $data['caseId'] . '">' . Escape::html($data['caseId']) . '</a>';
    }
}
