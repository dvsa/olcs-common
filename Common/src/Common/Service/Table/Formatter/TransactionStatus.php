<?php

/**
 * Transaction Status formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

// need to alias as RefData exists in Formatter namespace
use Common\RefData as Ref;

/**
 * Transaction Status formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TransactionStatus implements FormatterInterface
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
        switch ($row['status']['id']) {
            case Ref::TRANSACTION_STATUS_FAILED:
            case Ref::TRANSACTION_STATUS_CANCELLED:
                $statusClass = 'red';

                // if transaction is failed and it was migrated then change the status message
                if (isset($row['migratedFromOlbs']) && $row['migratedFromOlbs'] === true) {
                    $row['status']['description'] = 'Migrated';
                }
                break;
            case Ref::TRANSACTION_STATUS_PAID:
            case Ref::TRANSACTION_STATUS_COMPLETE:
                $statusClass = 'green';
                break;
            case Ref::TRANSACTION_STATUS_OUTSTANDING:
                $statusClass = 'orange';
                break;
            default:
                $statusClass = 'grey';
                break;
        }

        return sprintf(
            '<strong class="govuk-tag govuk-tag--%s">%s</strong>',
            $statusClass,
            $row['status']['description']
        );
    }
}
