<?php

namespace Common\Service\Table\Formatter;

use Common\RefData;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * EBSR document link
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class EbsrDocumentLink implements FormatterInterface
{
    const LINK_PATTERN = '<a class="govuk-link" href="%s">%s</a>';
    const URL_ROUTE = 'bus-registration/ebsr';
    const URL_ACTION = 'detail';

    /**
     * Formats the link to an EBSR document
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
                'id' => $data['id'],
                'action' => self::URL_ACTION
            ]
        );

        return sprintf(self::LINK_PATTERN, $url, $data['document']['description']);
    }
}
