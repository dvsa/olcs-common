<?php

namespace Common\View\Helper;

use PHPSandbox\PHPSandbox;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;

/**
 * Validates partials from the DB against function (and other) whitelists)
 */
class PartialSandbox extends AbstractHelper implements FactoryInterface
{
    private $sandbox;

    /**
     * Factory
     *
     * @param \Zend\View\HelperPluginManager $sl Service Manager
     *
     * @return $this;
     */
    public function createService(ServiceLocatorInterface $sl)
    {
        $this->sandbox = new PHPSandbox();
        $this->sandbox->whitelistFunc(['translate', 'url']);
        $this->sandbox->allow_escaping = true;

        return $this;
    }

    /**
     *
     * @param null $name Name of the partial to retrieve from Redis
     * @param string[] $values
     * @return string
     */
    public function __invoke($name = null, $values = [])
    {
        if (0 == func_num_args()) {
            return $this;
        }

        // Partial would actually be retrieved from reddit by key here, Static string for PoC and as retrieval method TBC.
        $partialString =
            '<h3>Sandbox PoC</h3>
                <ul>
                    <?php
                        if (isset($items) && !empty($items)) {
                        foreach ($items as $item) {
                            $url = $this->url($item[\'route\']);
                             echo translate($item[\'transkey\']);
                           }
                        }
                           ?>
                           <?php if(1): ?>
                               html stuff
                           <?php endif; ?>
                           </li>
                
                </ul>';

        return $this->validatePartial($partialString);
    }

    private function validatePartial($partialString)
    {
        //get offsets for PHP open tags
        preg_match('/<\?php/', $partialString, $openTags, PREG_OFFSET_CAPTURE);

        // There is nothing here to validate so return true
        if(count($openTags) == 0) {
            return true;
        }

        return $this->sandbox->validate(substr($partialString, $openTags[0][1]));
    }
}
