<?php

/**
 * task identifier formatter
 *
 * @author nick payne <nick.payne@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * task identifier formatter
 *
 * @author nick payne <nick.payne@valtech.co.uk>
 */
class TaskIdentifier implements FormatterInterface
{
    /**
     * Format a task identifier
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $identifier = $data['identifier'];
        if ($identifier === 'Unlinked') {
            return 'Unlinked';
        }
        $viewHelperManager = $sm->get('viewhelpermanager');
        $urlHelper = $viewHelperManager->get('url');
        $url = '#';
        switch ($data['linkType']) {
            case 'IRFO Organisation':
                break;
            case 'Bus Registration':
                break;
            case 'Application':
                break;
            case 'Case':
                break;
            case 'Licence':
                $url = $urlHelper('licence/overview', array('licence' => $data['linkId']));
                break;
            case 'Transport Manager':
                break;
            default:
                break;
        }
        $value = '<a href="' . $url . '">' . $data['identifier'] . '</a>';
        if ($data['licenceCount'] > 1) {
            $value .= ' (MLH)';
        }

        return $value;
    }
}
