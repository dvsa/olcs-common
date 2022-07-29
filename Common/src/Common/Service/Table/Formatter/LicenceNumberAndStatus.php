<?php

namespace Common\Service\Table\Formatter;

use Common\RefData;
use Common\Util\Escape;

class LicenceNumberAndStatus implements FormatterInterface
{
    /**
     * Format a licence number
     *
     * @param array                               $row            Row data
     * @param array                               $column         Column data
     * @param \Laminas\ServiceManager\ServiceManager $serviceLocator Service locator
     *
     * @return string
     * @inheritdoc
     */
    public static function format($row, $column = null, $serviceLocator = null)
    {
        $activeLink = true;

        if ($row['status']['id'] === RefData::LICENCE_STATUS_SURRENDER_UNDER_CONSIDERATION) {
            $activeLink = false;
        }

        $urlHelper = $serviceLocator->get('Helper\Url');

        $escapedLicNo = Escape::html($row['licNo']);

        if ($activeLink) {
            return self::markupWithLink($row, $urlHelper, $escapedLicNo);
        }

        return $escapedLicNo;
    }

    private static function markupWithLink($row, $urlHelper): string
    {
        return vsprintf(
            '<a class="govuk-link" href="%s">%s</a>',
            [
                $urlHelper->fromRoute('lva-licence', ['licence' => $row['id']]),
                $row['licNo'],
            ]
        );
    }
}
