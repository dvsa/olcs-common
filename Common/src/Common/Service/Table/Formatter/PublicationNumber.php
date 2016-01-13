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
    public static function format($data, $column = array(), $sm = null)
    {
        if ($data['pubStatus']['id'] === 'pub_s_new') {
            return $data['publicationNo'];
        }

        $uriPattern = $sm->get('Config')['document_share']['uri_pattern'];

        $url = sprintf($uriPattern, $data['document']['identifier']);

        return sprintf(
            '<a href="%s" data-file-url="%s" target="blank">%s</a>',
            $url,
            $url,
            $data['publicationNo']
        );
    }
}
