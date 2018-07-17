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
            case RefData::PERMIT_VALID:
                $statusClass .= ' green';
                break;
            case RefData::PERMIT_AWAITING:
                $statusClass .= ' orange';
                break;
            case RefData::PERMIT_EXPIRED:
                $statusClass .= ' red';
                break;
            case RefData::PERMIT_NYS:
                $statusClass .= ' grey';
                break;

            default:
                $statusClass .= ' grey';
                break;
        }

        $translator = $serviceLocator->get('translator');

        return vsprintf(
          '<span class="overview__%s">%s</span>',
          [
            $statusClass,
            $translator->translate($row['status']['description'])
          ]
        );
    }
}
