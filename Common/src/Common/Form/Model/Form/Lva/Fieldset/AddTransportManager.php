<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * Add transport manager fieldset
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AddTransportManager
{
    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "Select from registered users"
     * })
     * @Form\Type("Select")
     */
    public $registeredUser = null;
}
