<?php

/**
 * Fee Status formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

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
     * @param \Zend\ServiceManager\ServiceManager $serviceLocator
     * @return string
     */
    public static function format($row, $column = null, $serviceLocator = null)
    {
        $statusClass = 'status';
        switch ($row['feeStatus']['id']) {
            case 'lfs_ot':
                $statusClass .= ' red';
                break;
            case 'lfs_pd':
                $statusClass .= ' green';
                break;
            case 'lfs_wr':
                $statusClass .= ' orange';
                break;
            case 'lfs_w':
                $statusClass .= ' green';
                break;
            case 'lfs_cn':
                $statusClass .= ' grey';
                break;
            default:
                break;
        }
        return vsprintf(
            '%s <span class="%s">%s</span>',
            [$row['id'], $statusClass, $row['feeStatus']['description']]
        );
    }
}
