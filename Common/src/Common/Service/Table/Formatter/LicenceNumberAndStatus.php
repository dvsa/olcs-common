<?php

/**
 * Licence number and status formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

use Common\RefData;

/**
 * Licence number and status formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceNumberAndStatus implements FormatterInterface
{
    /**
     * Format a licence number and status
     *
     * @param array                               $row            Row data
     * @param array                               $column         Column data
     * @param \Zend\ServiceManager\ServiceManager $serviceLocator Service locator
     *
     * @return string
     * @inheritdoc
     */
    public static function format($row, $column = null, $serviceLocator = null)
    {
        $statusClass = 'status';
        switch ($row['status']['id']) {
            case RefData::LICENCE_STATUS_VALID:
                $statusClass .= ' green';
                break;
            case RefData::LICENCE_STATUS_SUSPENDED:
            case RefData::LICENCE_STATUS_CURTAILED:
            case RefData::LICENCE_STATUS_UNDER_CONSIDERATION:
            case RefData::LICENCE_STATUS_GRANTED:
                $statusClass .= ' orange';
                break;
            case RefData::LICENCE_STATUS_SURRENDERED:
            case RefData::LICENCE_STATUS_REVOKED:
            case RefData::LICENCE_STATUS_TERMINATED:
            case RefData::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT:
            case RefData::LICENCE_STATUS_WITHDRAWN:
            case RefData::LICENCE_STATUS_REFUSED:
            case RefData::LICENCE_STATUS_NOT_TAKEN_UP:
                $statusClass .= ' red';
                break;
            case RefData::LICENCE_STATUS_CANCELLED:
                $statusClass .= ' grey';
                break;
            default:
                $statusClass .= ' grey';
                break;
        }
        $urlHelper = $serviceLocator->get('Helper\Url');

        $translator = $serviceLocator->get('translator');
        if (isset($row['isExpired']) && $row['isExpired'] === true) {
            $row['status']['description'] = $translator->translate('licence.status.expired');
            $statusClass = 'status red';
        }

        if (isset($row['isExpiring']) && $row['isExpiring'] === true) {
            $row['status']['description'] = $translator->translate('licence.status.expiring');
            $statusClass = 'status red';
        }

        return vsprintf(
            '<a class="overview__link" href="%s"><span class="overview__link--underline">%s</span> <span class="overview__%s">%s</span></a>',
            [
                $urlHelper->fromRoute('lva-licence', ['licence' => $row['id']]),
                $row['licNo'],
                $statusClass,
                $row['status']['description']
            ]
        );
    }
}
