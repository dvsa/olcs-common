<?php

/**
 * Publication Number
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;

/**
 * Publication Number
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PublicationNumber implements FormatterInterface
{
    /**
     * Format
     *
     * @param array                               $data   The row data
     * @param array                               $column [OPTIONAL]
     * @param \Laminas\ServiceManager\ServiceManager $sm     [OPTIONAL]
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        if ($data['pubStatus']['id'] === 'pub_s_new') {
            return $data['publicationNo'];
        }

        $uriPattern = '/file/%s';
        $url = sprintf($uriPattern, $data['document']['id']);
        $linkPattern = '<a href="%s">%s</a>';
        $link = sprintf($linkPattern, Escape::html($url), Escape::html($data['publicationNo']));

        if ($data['pubStatus']['id'] === 'pub_s_generated') {
            $link = sprintf(
                '<a href="%s" data-file-url="%s" target="blank">%s</a>',
                htmlentities($data['webDavUrl'], ENT_QUOTES, 'utf-8'),
                htmlentities($data['webDavUrl'], ENT_QUOTES, 'utf-8'),
                htmlentities($data['publicationNo'], ENT_QUOTES, 'utf-8')
            );
        }

        return $link;
    }
}
