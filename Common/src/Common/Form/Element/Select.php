<?php

namespace Common\Form\Element;

use Common\Service\Data\PluginManager;
use Laminas\Form\Element\Select as LaminasSelect;

/**
 * Class DynamicSelect
 * @package Common\Form\Element
 */
class Select extends LaminasSelect
{
    public function __construct(
        $name = null,
        iterable $options = []
    ) {
        $this->setAttribute('class', 'govuk-select');
        parent::__construct($name, $options);
    }
}
