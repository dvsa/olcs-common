<?php

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Util\Escape;
use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;
use Laminas\View\HelperPluginManager;

/**
 * Bus reg number link
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegNumberLink implements FormatterPluginManagerInterface
{
    private const LINK_PATTERN = '<a class="govuk-link" href="%s">%s</a>';

    public const URL_ROUTE = 'licence/bus-details/service';
     //internal bus reg service details page
    public const LABEL_TRANSLATION_KEY = 'ebsr-link-label';

    public const LABEL_COLOUR = 'orange';

    protected TranslatorDelegator $translator;

    protected $viewHelperManager;

    protected UrlHelperService $urlHelper;

    /**
     * @param $viewHelperManager
     */
    public function __construct(TranslatorDelegator $translator, HelperPluginManager $viewHelperManager, UrlHelperService $urlHelper)
    {
        $this->translator = $translator;
        $this->viewHelperManager = $viewHelperManager;
        $this->urlHelper = $urlHelper;
    }

    /**
     * Formats the bus registration number with optional EBSR label
     *
     * @param array $data   data array
     * @param array $column column info
     *
     * @return string
     */
    public function format($data, $column = [])
    {
        $outputStatus = '';

        //if app is EBSR add a label
        if ($data['isTxcApp']) {
            $status = [
                'colour' => self::LABEL_COLOUR,
                'value' => $this->translator->translate(self::LABEL_TRANSLATION_KEY),
            ];

            $statusHelper = $this->viewHelperManager->get('status');
            $outputStatus = $statusHelper->__invoke($status);
        }

        $url = $this->urlHelper->fromRoute(self::URL_ROUTE, ['busRegId' => $data['id']], [], true);

        return sprintf(self::LINK_PATTERN, $url, Escape::html($data['regNo'])) . ' ' . $outputStatus;
    }
}
