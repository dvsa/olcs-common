<?php

/**
 * Status formatter
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;

/**
 * Status formatter
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
class LicencePermitReference implements FormatterInterface
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
        $urlHelper = $serviceLocator->get('Helper\Url');
        return vsprintf(
            '<a class="overview__link" href="%s"><span class="overview__link--underline">%s</span></a>',
            [
                $urlHelper->fromRoute('permits/application-overview', ['id' => $row['id']]),
                Escape::html($row['applicationRef'])
            ]
        );
    }
}
