<?php

/**
 * Publication Number
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Table\Formatter;

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
     * @param \Zend\ServiceManager\ServiceManager $sm     [OPTIONAL]
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
        $link = sprintf($linkPattern, $url, htmlentities($data['publicationNo']));

        if ($data['pubStatus']['id'] === 'pub_s_generated') {
            $uriPattern = $sm->get('Config')['document_share']['uri_pattern'];
            $url = sprintf($uriPattern, $data['document']['identifier']);
            $linkPattern = '<a href="%s" data-file-url="%s" target="blank">%s</a>';
            $link = sprintf($linkPattern, $url, $url, htmlentities($data['publicationNo']));
        }

        return $link;
    }
}
