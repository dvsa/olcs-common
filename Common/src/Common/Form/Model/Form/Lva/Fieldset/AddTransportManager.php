<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * Add Transport Manager fieldset
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AddTransportManager
{
    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "Choose a Transport Manager who’s already registered"
     * })
     * @Form\Type("Select")
     */
    public $registeredUser = null;

    /**
     * @Form\Attributes({"id":"addUser","type":"submit","class":"action--tertiary"})
     * @Form\Options({"label": "Or add a new Transport Manager"})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $addUser = null;
}
