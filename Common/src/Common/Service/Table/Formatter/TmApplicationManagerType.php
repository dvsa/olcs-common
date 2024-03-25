<?php

/**
 * Tm Application Manager Type formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;
use Laminas\Mvc\Application;

/**
 * Tm Application Manager Type formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TmApplicationManagerType implements FormatterPluginManagerInterface
{
    private Application $application;

    private UrlHelperService $urlHelper;

    private TranslatorDelegator $translator;

    public function __construct(Application $application, UrlHelperService $urlHelper, TranslatorDelegator $translator)
    {
        $this->application = $application;
        $this->urlHelper = $urlHelper;
        $this->translator = $translator;
    }

    /**
     * Tm Application Manager Type formatter
     *
     * @param  array $row
     * @param  array $column
     * @return string
     */
    public function format($row, $column = [])
    {
        $routeParams = [
            'id' => $row['id'],
            'action' => 'edit-tm-application',
            'transportManager' => $this->application
                ->getMvcEvent()
                ->getRouteMatch()
                ->getParam('transportManager')
        ];
        $url = $this->urlHelper->fromRoute(null, $routeParams);
        switch ($row['action']) {
            case 'A':
                $status = $this->translator->translate('tm_application.table.status.new');
                break;
            case 'U':
                $status = $this->translator->translate('tm_application.table.status.updated');
                break;
            case 'D':
                $status = $this->translator->translate('tm_application.table.status.removed');
                break;
            default:
                $status = '';
        }

        return $row['action'] === 'D' ? trim($row['tmType']['description']  . ' ' . $status) :
            '<a class="govuk-link" href="' . $url . '">' . trim($row['tmType']['description']  . ' ' . $status) . '</a>';
    }
}
