<?php

/**
 * Status formatter
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */

namespace Common\Service\Table\Formatter;

use Common\RefData;

/**
 * Status formatter
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
class Status implements FormatterInterface
{
    /**
     * status
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
        switch ($row['status']) {
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
            $row['status'] = $translator->translate('licence.status.expired');
            $statusClass = 'status red';
        }

        if (isset($row['isExpiring']) && $row['isExpiring'] === true) {
            $row['status'] = $translator->translate('licence.status.expiring');
            $statusClass = 'status red';
        }

        return vsprintf(
          '<span class="overview__%s">%s</span>',
          [
            $statusClass,
            $translator->translate($row['status'])
          ]
        );
    }
}
