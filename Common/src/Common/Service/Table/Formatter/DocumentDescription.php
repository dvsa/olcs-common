<?php

/**
 * Document Description Formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Table\Formatter;

use Common\Service\Table\Formatter\FormatterInterface;

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
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $urlHelper = $sm->get('Helper\Url');

        $url = $urlHelper->fromRoute(
            'getfile',
            [
                'file' => $data['documentStoreIdentifier'],
                'name' => $data['filename']
            ]
        );

        $attr = '';

        if (preg_match('/\.html$/', $data['filename'])) {
            $attr = 'target="_blank"';
        }

        return '<a href="' . $url . '" ' . $attr . '>' . $data['description'] . '</a>';
    }
}
