<?php

namespace Common\Service\Table\Formatter;

use Laminas\ServiceManager\ServiceManager;

/**
 * System info message link formatter
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
class SystemInfoMessageLink implements FormatterInterface
{
    const MAX_DESC_LEN = 50;

    /**
     * Format
     *
     * @param array          $data
     * @param array          $column
     * @param ServiceManager $sm
     *
     * @return string
     */
    public static function format($data, array $column = [], $sm = null)
    {
        //  define link
        $urlHelper = $sm->get('Helper\Url');

        $url = $urlHelper->fromRoute(
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

        $htmlLink = '<a href="' . $url . '" class="js-modal-ajax">' . $desc . '</a>';

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
