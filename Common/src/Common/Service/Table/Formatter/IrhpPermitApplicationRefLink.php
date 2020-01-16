<?php

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * IRHP Permit Application Ref Link formatter
 */
class IrhpPermitApplicationRefLink implements FormatterInterface
{
    /**
     * Format
     *
     * Returns the IRHP Permit Application Ref Link
     *
     * @param array                   $data   Row data
     * @param array                   $column Column Parameters
     * @param ServiceLocatorInterface $sm     Service Manager
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function format($data, array $column = [], ServiceLocatorInterface $sm = null)
    {
        return isset($data['irhpPermitApplication']['relatedApplication']) ? sprintf(
            '<a href="%s">%s</a>',
            $sm->get('Helper\Url')->fromRoute(
                'licence/irhp-application',
                [
                    'action' => 'index',
                    'licence' => $data['irhpPermitApplication']['relatedApplication']['licence']['id']
                ]
            ),
            Escape::html($data['irhpPermitApplication']['relatedApplication']['applicationRef'])
        ) : '';
    }
}
