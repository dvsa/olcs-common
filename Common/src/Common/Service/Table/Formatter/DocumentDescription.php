<?php

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
        $translator = $sm->get('translator');
        if (!isset($data['documentStoreIdentifier'])) {
            return self::getAnchor($data, $translator);
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

        return '<a href="' . $url . '" ' . $attr . '>' . self::getAnchor($data, $translator) . '</a>';
    }

    /**
     * Get anchor
     *
     * @param array $data data
     * @param \Zend\I18n\Translator\Translator $translator
     *
     * @return string
     */
    private static function getAnchor($data, $translator)
    {
        if (isset($data['description'])) {
            return $data['description'];
        }
        if (isset($data['filename'])) {
            return basename($data['filename']);
        }

        return $translator->translate('internal.document-description.formatter.no-description');
    }
}
