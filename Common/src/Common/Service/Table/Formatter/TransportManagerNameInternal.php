<?php

/**
 * TransportManagerName for Internal Formatter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\Service\Table\Formatter;

/**
 * TransportManagerName for Internal Formatter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManagerNameInternal extends TransportManagerName implements FormatterInterface
{
    /**
     * Get URL for the Transport managers name
     *
     * @param array $data
     *
     * @return string
     */
    protected static function getUrl($data, $sm)
    {
        $transportManagerId = $data['transportManager']['id'];
        $urlHelper = $sm->get('Helper\Url');
        $url = $urlHelper->fromRoute('transport-manager', ['transportManager' => $transportManagerId], [], true);

        return $url;
    }
}
