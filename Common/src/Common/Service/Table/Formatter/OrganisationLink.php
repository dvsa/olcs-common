<?php

/**
 * OrganisationLink.php
 */
namespace Common\Service\Table\Formatter;

use Common\Util\Escape;

/**
 * Class OrganisationLink
 *
 * Takes a organisation array and creates and outputs a link for that organisation.
 *
 * @package Common\Service\Table\Formatter
 */
class OrganisationLink implements FormatterInterface
{
    /**
     * Return a the organisation URL in a link format for a table.
     *
     * @param array $data The row data.
     * @param array $column The column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @inheritdoc
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $urlHelper = $sm->get('Helper\Url');
        $url = $urlHelper->fromRoute('operator/business-details', ['organisation' => $data['organisation']['id']]);

        return '<a href="' . $url . '">' . Escape::html($data['organisation']['name']) . '</a>';
    }
}
