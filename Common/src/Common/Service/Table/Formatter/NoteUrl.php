<?php

/**
 * Note URL formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * Note URL formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class NoteUrl implements FormatterInterface
{
    /**
     * Format a note URL
     *
     * @param array $row
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $serviceLocator
     * @return string
     * @inheritdoc
     */
    public static function format($row, $column = array(), $serviceLocator = null)
    {
        $request    = $serviceLocator->get('request');
        $urlHelper  = $serviceLocator->get('Helper\Url');

        $url = $urlHelper->fromRoute(
            null,
            ['action' => 'edit', 'id' => $row['id']],
            ['query' => $request->getQuery()->toArray()],
            true
        );

        return '<a class="govuk-link js-modal-ajax" href="' . $url . '">'
        . (new \DateTime($row['createdOn']))->format(\DATE_FORMAT) . '</a>';
    }
}
