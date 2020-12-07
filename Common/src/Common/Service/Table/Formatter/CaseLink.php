<?php

/**
 * Case Link
 */
namespace Common\Service\Table\Formatter;

/**
 * Case Link
 *
 * @package Common\Service\Table\Formatter
 */
class CaseLink implements FormatterInterface
{
    /**
     * Return a the case URL in a link format for a table.
     *
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        if (empty($data['id'])) {
            return '';
        }

        return sprintf(
            '<a href="%s">%s</a>',
            $sm->get('Helper\Url')->fromRoute(
                'case',
                [
                    'case' => $data['id']
                ]
            ),
            $data['id']
        );
    }
}
