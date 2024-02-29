<?php

/**
 * Internal licence permit reference formatter
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */

namespace Common\Service\Table\Formatter;

use Common\Rbac\Traits\Permission;
use Common\Service\Helper\UrlHelperService;
use Common\Util\Escape;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Internal licence permit reference formatter
 */
class InternalLicencePermitReference implements FormatterPluginManagerInterface
{
    use Permission;

    private UrlHelperService $urlHelper;

    public function __construct(UrlHelperService $urlHelper, AuthorizationService $authService)
    {
        $this->urlHelper = $urlHelper;
        $this->authService = $authService;
    }

    /**
     * status
     *
     * @param array $row    Row data
     * @param array $column Column data
     *
     * @return     string
     * @inheritdoc
     */
    public function format($row, $column = null)
    {
        $applicationRef = Escape::html($row['applicationRef']);

        if ($this->isInternalReadOnly()) {
            return $applicationRef;
        }

        $route = 'licence/irhp-application/application';
        $params = [
            'licence' => $row['licenceId'],
            'action' => 'edit',
            'irhpAppId' => $row['id']
        ];

        return vsprintf(
            '<a class="govuk-link" href="%s">%s</a>',
            [
                $this->urlHelper->fromRoute($route, $params),
                $applicationRef
            ]
        );
    }
}
