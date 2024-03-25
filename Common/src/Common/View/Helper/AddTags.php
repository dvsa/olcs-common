<?php

/**
 * Add tags view helper
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * Add tags view helper
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AddTags extends AbstractHelper
{
    private $tags = [
        // yes, slight repetition of the key phrase
        // but it'll be faster than using a back reference
        '\(if\s+applicable\)' => '<span class=js-hidden>(if applicable)</span>',
        // @TODO check welsh equivalent
        '\(os\s+yw\'n\s+berthnasol\)' => "<span class=js-hidden>(os yw'n berthnasol)</span>",
    ];

    /**
     * Render base asset path
     *
     * @return string
     */
    public function __invoke($str = null)
    {
        $search  = [];
        $replace = [];

        foreach ($this->tags as $s => $r) {
            $search[] = '#'.$s.'#';
            $replace[] = $r;
        }

        return preg_replace($search, $replace, $str);
    }
}
