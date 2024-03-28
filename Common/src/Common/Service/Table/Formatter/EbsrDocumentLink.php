<?php

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;

/**
 * EBSR document link
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class EbsrDocumentLink implements FormatterPluginManagerInterface
{
    public const LINK_PATTERN = '<a class="govuk-link" href="%s">%s</a>';

    public const URL_ROUTE = 'bus-registration/ebsr';

    public const URL_ACTION = 'detail';

    private UrlHelperService $urlHelper;

    public function __construct(UrlHelperService $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    /**
     * Formats the link to an EBSR document
     *
     * @param array $data   data array
     * @param array $column column info
     *
     * @return string
     */
    public function format($data, $column = [])
    {
        $url = $this->urlHelper->fromRoute(
            self::URL_ROUTE,
            [
                'id' => $data['id'],
                'action' => self::URL_ACTION
            ]
        );

        return sprintf(self::LINK_PATTERN, $url, $data['document']['description']);
    }
}
