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
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
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
        $identifier = $data['linkDisplay'];
        if ($identifier === 'Unlinked') {
            return 'Unlinked';
        }

        $urlHelper = $sm->get('Helper\Url');
        $url = '#';
        switch ($data['linkType']) {
            case 'Licence':
                $url = $urlHelper->fromRoute('lva-licence/overview', array('licence' => $data['linkId']));
                break;
            case 'Application':
                $url = $urlHelper->fromRoute('lva-application/overview', array('application' => $data['linkId']));
                break;
            case 'Transport Manager':
                $url = $urlHelper->fromRoute('transport-manager', array('transportManager' => $data['linkId']));
                break;
            case 'Case':
                $url = $urlHelper->fromRoute('case', array('case' => $data['linkId']));
                break;
            case 'Bus Registration':
                $url = $urlHelper->fromRoute(
                    'licence/bus-details',
                    array('busRegId' => $data['linkId'], 'licence' => $data['licenceId'])
                );
                break;
            default:
                break;
        }
        $value = '<a href="' . $url . '">' . $data['linkDisplay'] . '</a>';
        if ($data['licenceCount'] > 1) {
            $value .= ' (MLH)';
        }

        return $value;
    }
}
