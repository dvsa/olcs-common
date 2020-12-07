<?php

/**
 * Name Action And Status formatter
 */

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;
use Laminas\ServiceManager\ServiceManager;

/**
 * Name Action And Status formatter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class NameActionAndStatus implements FormatterInterface
{

    /**
     * Format a name with default edit action & associated status
     *
     * @param array          $data   data row
     * @param array          $column column specification
     * @param ServiceManager $sm     SM
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $title = !empty($data['title']['description']) ? $data['title']['description'] . ' ' : '';
        $return = '<input class="" name="table[action][edit][' . intval($data['id']) . ']" value="'
            . Escape::html($title . $data['forename'] . ' ' . $data['familyName'])
            . '" type="submit">';

        if (isset($data['status']) && ($data['status'] == 'new')) {
            $return .= ' <span class="overview__status green">New</span>';
        }

        return $return;
    }
}
