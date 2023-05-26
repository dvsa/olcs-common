<?php

/**
 * Internal licence permit reference formatter
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Util\Escape;

/**
 * Internal licence permit reference formatter
 */
class InternalLicencePermitReference implements FormatterPluginManagerInterface
{
    private UrlHelperService $urlHelper;

    /**
     * @param UrlHelperService $urlHelper
     */
    public function __construct(UrlHelperService $urlHelper)
    {
        $this->urlHelper = $urlHelper;
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
                Escape::html($row['applicationRef'])
            ]
        );
    }
}
