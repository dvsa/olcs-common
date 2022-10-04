<?php

/**
 * Variation LVA service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Lva;

use Common\Service\Helper\GuidanceHelperService;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Helper\UrlHelperService;

/**
 * Variation LVA service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationLvaService
{
    /** @var TranslationHelperService */
    private $translationHelper;

    /** @var GuidanceHelperService */
    private $guidanceHelper;

    /** @var UrlHelperService */
    private $urlHelper;

    public function __construct(
        TranslationHelperService $translationHelper,
        GuidanceHelperService $guidanceHelper,
        UrlHelperService $urlHelper
    ) {
        $this->translationHelper = $translationHelper;
        $this->guidanceHelper = $guidanceHelper;
        $this->urlHelper = $urlHelper;
    }

    /**
     * add variation message
     *
     * @param int         $licenceId     licence id
     * @param null|string $redirectRoute route for redirect
     * @param string      $msgKey        message key
     *
     * @return void
     */
    public function addVariationMessage($licenceId, $redirectRoute = null, $msgKey = 'variation-message')
    {
        $link = $this->getVariationLink($licenceId, $redirectRoute);

        $message = $this->translationHelper->translateReplace($msgKey, [$link]);

        $this->guidanceHelper->append($message);
    }

    /**
     * get variation link
     *
     * @param int         $licenceId     licence id
     * @param string|null $redirectRoute route for redirect
     *
     * @return string
     */
    public function getVariationLink($licenceId, $redirectRoute = null)
    {
        return $this->urlHelper->fromRoute(
            'lva-licence/variation',
            ['licence' => $licenceId, 'redirectRoute' => $redirectRoute]
        );
    }
}
