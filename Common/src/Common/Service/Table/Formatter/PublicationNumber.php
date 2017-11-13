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
        
        $url = sprintf('/file/%s', $data['document']['id']);
        return sprintf(
            '<a href="%s" data-file-url="%s" target="blank">%s</a>',
            $url,
            $url,
            $data['publicationNo']
        );
    }
}
