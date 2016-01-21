<?php

/**
 * AccessedCorrespondence.php
 */
namespace Common\Service\Table\Formatter;

/**
 * Class AccessedCorrespondence
 *
 * Accessed correspondence formatter, displays correspondence as a link to the document and
 * denotes whether the correspondence has been accessed.
 *
 * @package Common\Service\Table\Formatter
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class AccessedCorrespondence implements FormatterInterface
{
    /**
     * Get a link for the document with the access indicator.
     *
     * @param array $data The row data.
     * @param array $column The column data.
     * @param null $sm The service manager.
     *
     * @return string The document link and accessed indicator
     */
    public static function format($data, $column = array(), $sm = null)
    {
        unset($column);

        $url = $sm->get('Helper\Url')->fromRoute(
            'correspondence/access',
            array(
                'correspondenceId' => $data['correspondence']['id']
            )
        );

        $title = '';
        if ($data['correspondence']['accessed'] === 'N') {
            $title .= '<span class="status green">New</span> ';
        }

        return sprintf(
            '<a class="strong" href="%s"><b>%s</b></a>%s',
            $url,
            $data['correspondence']['document']['description'],
            $title
        );
    }
}
