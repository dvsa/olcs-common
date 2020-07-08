<?php

/**
 * IrhpPermitNumberInternal formatter
 */

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;

class IrhpPermitNumberInternal implements FormatterInterface
{
    /**
     * @inheritdoc
     */
    public static function format($row, $column = null, $serviceLocator = null)
    {
        $route = 'licence/irhp-permits/permit';
        $params = [
            'licence' => $row['irhpPermitApplication']['relatedApplication']['licence']['id'],
        ];
        $options = [
            'query' => [
                'irhpPermitType' => $row['irhpPermitRange']['irhpPermitStock']['irhpPermitType']['id']
            ]
        ];

        return vsprintf(
            '<a href="%s">%s</a>',
            [
                $serviceLocator->get('Helper\Url')->fromRoute($route, $params, $options),
                Escape::html($row['permitNumber'])
            ]
        );
    }
}
