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
     * @param array $data The row data
     *
     * @return string
     */
    public static function format($data)
    {
        if ($data['pubStatus']['id'] === 'pub_s_new') {
            return $data['publicationNo'];
        }

        $url = sprintf('/file/%s', $data['document']['id']);
        return sprintf(
            '<a href="%s">%s</a>',
            $url,
            $data['publicationNo']
        );
    }
}
