<?php

/**
 * Licence number and status formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

use Common\RefData;
use Common\Util\Escape;

/**
 * Licence status is shown slightly differently in selfserve, with certain statuses mapped to "expired" status
 */
class LicenceStatusSelfserve implements FormatterInterface
{
    const MARKUP_FORMAT = '<span class="govuk-tag govuk-tag--%s">%s</span>';

    /**
     * Format a licence number and status
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
        switch ($row['status']['id']) {
            case RefData::LICENCE_STATUS_VALID:
            case RefData::LICENCE_STATUS_SURRENDER_UNDER_CONSIDERATION:
                $statusClass = 'green';
                break;
            case RefData::LICENCE_STATUS_SUSPENDED:
            case RefData::LICENCE_STATUS_CURTAILED:
            case RefData::LICENCE_STATUS_UNDER_CONSIDERATION:
            case RefData::LICENCE_STATUS_GRANTED:
                $statusClass = 'orange';
                break;
            case RefData::LICENCE_STATUS_SURRENDERED:
            case RefData::LICENCE_STATUS_REVOKED:
            case RefData::LICENCE_STATUS_TERMINATED:
            case RefData::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT:
            case RefData::LICENCE_STATUS_WITHDRAWN:
            case RefData::LICENCE_STATUS_REFUSED:
            case RefData::LICENCE_STATUS_NOT_TAKEN_UP:
                $statusClass = 'red';
                break;
            case RefData::LICENCE_STATUS_CANCELLED:
            default:
                $statusClass = 'grey';
                break;
        }

        $translator = $serviceLocator->get('translator');

        if ($row['status']['id'] !== RefData::LICENCE_STATUS_SURRENDER_UNDER_CONSIDERATION) {
            [$row, $statusClass] = self::changeStateIfExpired($row, $statusClass);
        }

        return sprintf(
            self::MARKUP_FORMAT,
            $statusClass,
            Escape::html($translator->translate($row['status']['description']))
        );
    }

    protected static function changeStateIfExpired(array $row, string $statusClass): array
    {
        if (isset($row['isExpired']) && $row['isExpired'] === true) {
            $row['status']['description'] = 'licence.status.expired';
            $statusClass = 'red';
        }

        if (isset($row['isExpiring']) && $row['isExpiring'] === true) {
            $row['status']['description'] = 'licence.status.expiring';
            $statusClass = 'red';
        }
        return [$row, $statusClass];
    }
}
