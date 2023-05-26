<?php

/**
 * Name Action And Status formatter
 */

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;

/**
 * Name Action And Status formatter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class NameActionAndStatus implements FormatterPluginManagerInterface
{

    public const BUTTON_FORMAT = '<button data-prevent-double-click="true" class="action-button-link" role="link" '
    . 'data-module="govuk-button" type="submit" name="table[action][edit][%d]">%s</button>';

    /**
     * Format a name with default edit action & associated status
     *
     * @param array $data   data row
     * @param array $column column specification
     *
     * @return string
     */
    public function format($data, $column = [])
    {
        $title = !empty($data['title']['description']) ? $data['title']['description'] . ' ' : '';
        $return = sprintf(
            self::BUTTON_FORMAT,
            intval($data['id']),
            Escape::html($title . $data['forename'] . ' ' . $data['familyName'])
        );

        if (isset($data['status']) && ($data['status'] == 'new')) {
            $return .= ' <span class="overview__status green">New</span>';
        }

        return $return;
    }
}
