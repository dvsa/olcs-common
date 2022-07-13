<?php

/**
 * Internal licence permit reference formatter
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */

namespace Common\Service\Table\Formatter;

use Common\RefData;
use Common\Util\Escape;

/**
 * Internal licence permit reference formatter
 */
class InternalLicencePermitReference implements FormatterInterface
{
    /**
     * status
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
        $route = 'licence/irhp-application/application';
        $params = [
            'licence' => $row['licenceId'],
            'action' => 'edit',
            'irhpAppId' => $row['id']
        ];

        return vsprintf(
            '<a class="govuk-link" href="%s">%s</a>',
            [
                $serviceLocator->get('Helper\Url')->fromRoute($route, $params),
                Escape::html($row['applicationRef'])
            ]
        );
    }
}
