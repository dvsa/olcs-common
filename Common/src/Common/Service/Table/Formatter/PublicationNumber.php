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
            $osType = $data['userOsType']['id'] ?? 'windows_7';
            $documentConfig = $sm->get('Config');
            $uriPattern = $documentConfig[$osType . '_document_share']['uri_pattern'] ?? $documentConfig['document_share']['uri_pattern'];
            $url = sprintf($uriPattern, $data['document']['identifier']);
            $linkPattern = '<a href="%s" data-file-url="%s" data-os-type="'.$osType.'" target="blank">%s</a>';
            $link = sprintf($linkPattern, Escape::html($url), Escape::html($url), Escape::html($data['publicationNo']));
        }

        return $link;
    }
}
