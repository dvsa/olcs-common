<?php

declare(strict_types=1);

namespace Common\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * Produces a standard link with all necessary escaping
 * defaults to a govuk-link css class
 * provides a default "opens in new tab" message for screen readers
 * external links add target="_blank" and rel="external noreferrer noopener"
 * "noreferrer noopener" prevents reverse tabnabbing attacks
 *
 * @see https://design-system.service.gov.uk/styles/typography/#links
 * @see https://owasp.org/www-community/attacks/Reverse_Tabnabbing
 */
class LinkNewWindow extends AbstractHelper
{
    const LINK_FORMAT = '<a href="%s" class="%s" target="_blank">%s<span class="govuk-visually-hidden">%s</span></a>';
    const LINK_FORMAT_EXTERNAL = '<a href="%s" class="%s" target="_blank" rel="external noreferrer noopener">%s<span class="govuk-visually-hidden">%s</span></a>';

    public function __invoke(
        string $url,
        string $linkText,
        string $class = 'govuk-link',
        string $screenReaderText = 'link.opens-new-window',
        bool $isExternal = false
    ): string
    {
        $escapedUrl = $this->view->escapeHtmlAttr($url);
        $escapedText = $this->view->escapeHtml($this->view->translate($linkText));
        $escapedScreenReaderText = $this->view->escapeHtml($this->view->translate($screenReaderText));
        $escapedClass = $this->view->escapeHtmlAttr($class);

        $linkFormat = self::LINK_FORMAT;

        if ($isExternal) {
            $linkFormat = self::LINK_FORMAT_EXTERNAL;
        }

        return sprintf($linkFormat, $escapedUrl, $escapedClass, $escapedText, $escapedScreenReaderText);
    }
}
