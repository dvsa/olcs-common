<?php

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;

/**
 * System info message link formatter
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
class SystemInfoMessageLink implements FormatterPluginManagerInterface
{
    public const MAX_DESC_LEN = 50;
    private UrlHelperService $urlHelper;

    /**
     * @param UrlHelperService $urlHelper
     */
    public function __construct(UrlHelperService $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    /**
     * Format
     *
     * @param array $data
     * @param array $column
     *
     * @return string
     */
    public function format($data, $column = [])
    {
        $url = $this->urlHelper->fromRoute(
            'admin-dashboard/admin-system-info-message',
            [
                'action' => 'edit',
                'msgId' => $data['id'],
            ]
        );

        $desc = $data['description'];
        if (strlen($desc) > self::MAX_DESC_LEN) {
            $desc = substr($desc, 0, self::MAX_DESC_LEN) . '...';
        }

        $htmlLink = '<a href="' . $url . '" class="govuk-link js-modal-ajax">' . $desc . '</a>';

        //  define status
        if ($data['isActive']) {
            $statusParams = ['green', 'ACTIVE'];
        } else {
            $statusParams = ['grey', 'INACTIVE'];
        }

        $htmlStatus = vsprintf(' <span class="status %s">%s</span>', $statusParams);

        return $htmlLink . $htmlStatus;
    }
}
