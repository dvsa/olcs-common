<?php

/**
 * Document Description Formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Table\Formatter;

/**
 * Document Description Formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DocumentDescription implements FormatterInterface
{
    /**
     * Format a cell
     *
     * @param array                               $data   Row data
     * @param array                               $column Column data
     * @param \Zend\ServiceManager\ServiceManager $sm     Service manager
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        if (!isset($data['documentStoreIdentifier'])) {
            return $data['description'];
        }

        $urlHelper = $sm->get('Helper\Url');

        $url = $urlHelper->fromRoute(
            'getfile',
            [
                'identifier' => $data['id']
            ]
        );

        $attr = '';

        if (preg_match('/\.html$/', $data['documentStoreIdentifier'])) {
            $attr = 'target="_blank"';
        }

        return '<a href="' . $url . '" ' . $attr . '>' . $data['description'] . '</a>';
    }
}
