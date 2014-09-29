<?php

/**
 * Add tags view helper
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\View\Helper;

use Zend\View\Helper\HelperInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Add tags view helper
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AddTags extends AbstractHelper implements HelperInterface
{
    private $tags = [
        // yes, slight repetition of the key phrase
        // but it'll be faster than using a backreference
        // @TODO welsh equivalent when we know what it is
        '\(if\s+applicable\)' => '<span class=js-hidden>(if applicable)</span>'
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
