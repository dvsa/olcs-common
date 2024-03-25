<?php

namespace Common\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Helper\UrlHelperService;
use Common\Util\Escape;

class LicenceNumberAndStatus implements FormatterPluginManagerInterface
{
    private UrlHelperService $urlHelper;

    public function __construct(UrlHelperService $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    /**
     * Format a licence number
     *
     * @param array $row    Row data
     * @param array $column Column data
     *
     * @return     string
     * @inheritdoc
     */
    public function format($row, $column = null)
    {
        $activeLink = true;

        if ($row['status']['id'] === RefData::LICENCE_STATUS_SURRENDER_UNDER_CONSIDERATION) {
            $activeLink = false;
        }

        $escapedLicNo = Escape::html($row['licNo']);

        if ($activeLink) {
            return self::markupWithLink($row);
        }

        return $escapedLicNo;
    }

    private function markupWithLink($row): string
    {
        return vsprintf(
            '<a class="govuk-link" href="%s">%s</a>',
            [
                $this->urlHelper->fromRoute('lva-licence', ['licence' => $row['id']]),
                $row['licNo'],
            ]
        );
    }
}
