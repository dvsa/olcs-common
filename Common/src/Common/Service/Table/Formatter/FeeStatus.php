<?php

/**
 * Fee Status formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

use Common\RefData;

/**
 * Fee Status formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeStatus implements FormatterInterface
{
    /**
     * Format a fee status
     *
     * @param array $row
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $serviceLocator
     * @return string
     * @inheritdoc
     */
    public static function format($row, $column = null, $serviceLocator = null)
    {
        switch ($row['feeStatus']['id']) {
            case RefData::FEE_STATUS_PAID:
                $statusClass = 'green';
                break;
            case RefData::FEE_STATUS_OUTSTANDING:
                $statusClass = 'orange';
                break;
            case RefData::FEE_STATUS_CANCELLED:
                $statusClass = 'red';
                break;
            default:
                $statusClass = 'grey';
                break;
        }
        return vsprintf(
            '<strong class="govuk-tag govuk-tag--%s">%s</strong>',
            [$statusClass, $row['feeStatus']['description']]
        );
    }
}
