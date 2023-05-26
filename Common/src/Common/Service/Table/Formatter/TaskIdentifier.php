<?php

/**
 * task identifier formatter
 *
 * @author nick payne <nick.payne@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;

/**
 * task identifier formatter
 *
 * @author nick payne <nick.payne@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TaskIdentifier implements FormatterPluginManagerInterface
{
    private UrlHelperService $urlHelper;

    /**
     * @param UrlHelperService $urlHelper
     */
    public function __construct(UrlHelperService $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }
    /**
     * Format a task identifier
     *
     * @param  array $data
     * @param  array $column
     * @return string
     */
    public function format($data, $column = [])
    {
        $identifier = $data['linkDisplay'];
        if ($identifier === 'Unlinked') {
            return 'Unlinked';
        }

        $url = '#';
        switch ($data['linkType']) {
            case 'Licence':
                $url = $this->urlHelper->fromRoute('lva-licence/overview', array('licence' => $data['linkId']));
                break;
            case 'Application':
                $url = $this->urlHelper->fromRoute('lva-application/overview', array('application' => $data['linkId']));
                break;
            case 'Transport Manager':
                $url = $this->urlHelper->fromRoute('transport-manager/details', array('transportManager' => $data['linkId']));
                break;
            case 'Case':
                $url = $this->urlHelper->fromRoute('case', array('case' => $data['linkId']));
                break;
            case 'Bus Registration':
                $url = $this->urlHelper->fromRoute(
                    'licence/bus-details',
                    array('busRegId' => $data['linkId'], 'licence' => $data['licenceId'])
                );
                break;
            case 'IRFO Organisation':
                $url = $this->urlHelper->fromRoute('operator/business-details', array('organisation' => $data['linkId']));
                break;
            case 'Submission':
                $url = $this->urlHelper->fromRoute(
                    'submission',
                    array('case' => $data['caseId'], 'submission' => $data['linkId'], 'action' => 'details')
                );
                break;
            case 'Permit Application':
                $url = $this->urlHelper->fromRoute(
                    'licence/irhp-application/application',
                    array(
                    'irhpAppId' => $data['linkId'],
                    'licence' => $data['licenceId'],
                    'action' => 'edit'
                    )
                );
                break;
            default:
                break;
        }
        $value = '<a class="govuk-link" href="' . $url . '">' . $data['linkDisplay'] . '</a>';

        return $value;
    }
}
