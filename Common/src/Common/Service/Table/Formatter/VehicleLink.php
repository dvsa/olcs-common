<?php

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;

/**
 * Vehicle Link
 *
 * @package Common\Service\Table\Formatter
 */
class VehicleLink implements FormatterPluginManagerInterface
{
    private UrlHelperService $urlHelper;

    public function __construct(UrlHelperService $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    /**
     * Return a vehicle URL in a link format for a table.
     *
     * @param array $data
     * @param array $column
     *
     * @return string
     */
    public function format($data, $column = [])
    {
        return sprintf(
            '<a class="govuk-link" href="%s">%s</a>',
            $this->urlHelper->fromRoute(
                'licence/vehicle/view/GET',
                [
                    'vehicle' => $data['vehicle']['id']
                ],
                [],
                true
            ),
            $data['vehicle']['vrm']
        );
    }
}
