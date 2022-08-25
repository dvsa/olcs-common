<?php

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Feature toggle link
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class FeatureToggleEditLink implements FormatterInterface
{
    const LINK_PATTERN = '<a href="%s" class="govuk-link js-modal-ajax">%s</a>';
    const URL_ROUTE = 'admin-dashboard/admin-feature-toggle';
    const URL_ACTION = 'edit';

    /**
     * Formats the link to a feature toggle record
     *
     * @param array                        $data   data array
     * @param array                        $column column info
     * @param null|ServiceLocatorInterface $sm     service locator
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        /** @var \Common\Service\Helper\UrlHelperService $statusHelper */
        $urlHelper = $sm->get('Helper\Url');

        $url = $urlHelper->fromRoute(
            self::URL_ROUTE,
            [
                'id' => (int)$data['id'],
                'action' => self::URL_ACTION
            ]
        );

        return sprintf(self::LINK_PATTERN, $url, Escape::Html($data['friendlyName']));
    }
}
