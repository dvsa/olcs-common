<?php

namespace Common\Service\Table\Formatter;

use Common\RefData;

/**
 * EBSR document link
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class EbsrDocumentLink implements FormatterInterface
{
    const LINK_PATTERN = '<a href="%s">%s</a><span class="status %s">%s</span>';

    public static function format($data, $column = array(), $sm = null)
    {
        $urlHelper = $sm->get('Helper\Url');

        $url = $urlHelper->fromRoute(
            'getfile',
            [
                'identifier' => $data['document']['id']
            ]
        );

        switch($data['ebsrSubmissionStatus']['id']) {
            case RefData::EBSR_STATUS_PROCESSING:
            case RefData::EBSR_STATUS_VALIDATING:
            case RefData::EBSR_STATUS_SUBMITTED:
                $colour = 'orange';
                $label = 'processing';
                break;
            case RefData::EBSR_STATUS_PROCESSED:
                $colour = 'green';
                $label = 'successful';
                break;
            default:
                $colour = 'red';
                $label = 'failed';
        }

        return sprintf(self::LINK_PATTERN, $url, $data['document']['description'], $colour, $label);
    }
}
