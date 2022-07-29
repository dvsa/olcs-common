<?php

namespace Common\Service\Table\Formatter;

use Laminas\ServiceManager\ServiceLocatorInterface;

class EbsrRegNumberLink implements FormatterInterface
{
    const LINK_PATTERN = '<a class="govuk-link" href="%s">%s</a>';
    const URL_ROUTE = 'bus-registration/details';

    /**
     * Formats the ebsr registration number
     *
     * @param array                        $data   data array
     * @param array                        $column column info
     * @param null|ServiceLocatorInterface $sm     service locator
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        //standardise the format of the data, so this can be used by multiple tables
        //we set the data even if the busReg key is blank
        if (array_key_exists('busReg', $data)) {
            $data = $data['busReg'];
        }

        if (!isset($data['id'])) {
            return '';
        }

        $urlHelper = $sm->get('Helper\Url');

        $url = $urlHelper->fromRoute(
            self::URL_ROUTE,
            [
                'busRegId' => $data['id']
            ]
        );

        return sprintf(self::LINK_PATTERN, $url, $data['regNo']);
    }
}
