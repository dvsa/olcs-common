<?php

namespace Common\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Helper\UrlHelperService;
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
class SearchCasesName implements FormatterPluginManagerInterface
{
    private AuthorizationService $authService;
    private UrlHelperService $urlHelper;

    public function __construct(AuthorizationService $authService, UrlHelperService $urlHelper)
    {
        $this->authService = $authService;
        $this->urlHelper = $urlHelper;
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
        if (!empty($data['tmId'])) {
            $url = $this->urlHelper->fromRoute(
                'transport-manager/details',
                ['transportManager' => $data['tmId']]
            );
            $link = $data['tmForename'] . ' ' . $data['tmFamilyName'];
        } else {
            $url = $this->urlHelper->fromRoute(
                'operator/business-details',
                ['organisation' => $data['orgId']]
            );
            $link = $data['orgName'];
        }

        if ($this->authService->isGranted(RefData::PERMISSION_INTERNAL_IRHP_ADMIN)) {
            return Escape::html($link);
        }

        return '<a class="govuk-link" href="' . $url . '">' . Escape::html($link) . '</a>';
    }
}
