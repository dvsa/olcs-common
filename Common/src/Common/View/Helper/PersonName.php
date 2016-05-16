<?php

namespace Common\View\Helper;

use Common\View\Helper\Traits\Utils;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\EscapeHtml;

/**
 * PersonName view helper
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class PersonName extends AbstractHelper
{
    use Utils;

    /**
     * Get the HTML to render a persons name
     *
     * @param array $address
     *
     * @return string HTML
     */
    public function __invoke($person = [], $fields = null)
    {
        if ($fields === null) {
            $fields = [
                'title',
                'forename',
                'familyName',
            ];
        }

        $parts = [];

        foreach ($fields as $item) {
            if (isset($person[$item]) && !empty($person[$item])) {
                $parts[] = $this->escapeHtml($person[$item]);
            }
        }

        return implode(' ', $parts);
    }
}
