<?php

/**
 * Get Placeholder
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\View\Helper;

use Zend\View\Model\ViewModel;

/**
 * Get Placeholder
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GetPlaceholder
{
    private $value;

    public function __construct($container = null)
    {
        $this->value = $container->getValue();
    }

    public function asString()
    {
        if (is_array($this->value)) {
            $this->value = reset($this->value);
        }

        if (is_string($this->value)) {
            return (string)$this->value;
        }

        return null;
    }

    public function asView()
    {
        if (is_array($this->value)) {
            $this->value = reset($this->value);
        }

        if ($this->value instanceof ViewModel) {
            return $this->value;
        }

        return null;
    }

    public function asObject()
    {
        if (is_array($this->value)) {
            $this->value = reset($this->value);
        }

        if (is_object($this->value)) {
            return $this->value;
        }

        return null;
    }
}
