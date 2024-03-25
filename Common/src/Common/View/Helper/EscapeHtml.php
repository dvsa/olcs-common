<?php

namespace Common\View\Helper;

use HTMLPurifier;
use Laminas\View\Helper\AbstractHelper;

/**
 * EscapeHtml with whitelisted tags
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class EscapeHtml extends AbstractHelper
{
    /** @var HtmlPurifier $htmlPurifierService */
    private $htmlPurifierService;

    /**
     * EscapeHtml constructor.
     *
     * @return void
     */
    public function __construct(HtmlPurifier $htmlPurifierService)
    {
        $this->htmlPurifierService = $htmlPurifierService;
    }

    /**
     * @param string $toEscape
     */
    public function __invoke($toEscape): string
    {
        if (is_null($toEscape)) {
            return '';
        }

        return $this->htmlPurifierService->purify($toEscape);
    }
}
