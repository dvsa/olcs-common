<?php

namespace Common\Service\Table\Formatter;

use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class AccessedCorrespondence
 *
 * Accessed correspondence formatter, displays correspondence as a link to the document and
 * denotes whether the correspondence has been accessed.
 *
 * @package Common\Service\Table\Formatter
 *
 * @author  Josh Curtis <josh.curtis@valtech.co.uk>
 */
class AccessedCorrespondence implements FormatterInterface
{
    /**
     * Get a link for the document with the access indicator.
     *
     * @param array                   $data   The row data.
     * @param array                   $column The column data.
     * @param ServiceLocatorInterface $sm     The service manager.
     *
     * @return string The document link and accessed indicator
     */
    public static function format($data, $column = array(), ServiceLocatorInterface $sm = null)
    {

        $url = $sm->get('Helper\Url')->fromRoute(
            'correspondence/access',
            [
                'correspondenceId' => $data['correspondence']['id'],
            ]
        );

        $title = '';
        if ($data['correspondence']['accessed'] === 'N') {
            $title .= '<span class="status green">' .
                $sm->get('translator')->translate('dashboard-correspondence.table.status.new') .
                '</span> ';
        }

        $extension = pathinfo($data['correspondence']['document']['filename'], PATHINFO_EXTENSION);
        if (!empty($extension)) {
            $extension = ' (' . $extension . ')';
        }

        return sprintf(
            '<a class="strong" href="%s"><b>%s%s</b></a>%s',
            $url,
            $data['correspondence']['document']['description'],
            $extension,
            $title
        );
    }
}
