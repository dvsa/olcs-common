<?php

namespace Common\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Helper\UrlHelperService;

/**
 * Dashboard Application Link
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class DashboardApplicationLink implements FormatterPluginManagerInterface
{
    public function __construct(private UrlHelperService $urlHelper)
    {
    }

    /**
     * Format column value
     *
     * @param array $data   Row data
     * @param array $column Column Parameters
     *
     * @return string
     */
    #[\Override]
    public function format($data, $column = [])
    {
        if ($data['status']['id'] !== RefData::APPLICATION_STATUS_NOT_SUBMITTED) {
            $route = 'lva-' . $column['lva'] . '/submission-summary';
        } else {
            $route = 'lva-' . $column['lva'];
        }

        $url = $this->urlHelper->fromRoute($route, ['application' => $data['id']]);

        return vsprintf(
            '<a class="govuk-link" href="%s">%s</a>',
            [
                $url,
                isset($data['licNo']) ? $data['licNo'] . '/' . $data['id'] : $data['id'],
            ]
        );
    }
}
