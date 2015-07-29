<?php

/**
 * PersonName view helper
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */

namespace Common\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * PersonName view helper
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class PersonName extends AbstractHelper
{
    /**
     * Get the HTML to render a persons name
     *
     * @param array $address
     *
     * @return string HTML
     */
    public function __invoke(
        array $person,
        $fields = [
            'title',
            'forename',
            'familyName'
        ]
    ) {
        $parts = array();

        foreach ($fields as $item) {
            if (isset($person[$item]) && !empty($person[$item])) {
                $parts[] = $person[$item];
            }
        }

        return implode(' ', $parts);
    }
}
