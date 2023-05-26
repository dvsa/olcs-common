<?php

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Util\Escape;

/**
 * Feature toggle link
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class FeatureToggleEditLink implements FormatterPluginManagerInterface
{
    private UrlHelperService $urlHelper;
    public const LINK_PATTERN = '<a href="%s" class="govuk-link js-modal-ajax">%s</a>';
    public const URL_ROUTE = 'admin-dashboard/admin-feature-toggle';
    public const URL_ACTION = 'edit';

    /**
     * @param UrlHelperService $urlHelper
     */
    public function __construct(UrlHelperService $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    /**
     * Formats the link to a feature toggle record
     *
     * @param array $data   data array
     * @param array $column column info
     *
     * @return string
     */
    public function format($data, $column = [])
    {
        $url = $this->urlHelper->fromRoute(
            self::URL_ROUTE,
            [
                'id' => (int)$data['id'],
                'action' => self::URL_ACTION
            ]
        );

        return sprintf(self::LINK_PATTERN, $url, Escape::Html($data['friendlyName']));
    }
}
