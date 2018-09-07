<?php

namespace Common\Form\Model\Form\Lva\Fieldset\TransportManager;

use Zend\Form\Annotation as Form;

/**
 * @Form\Attributes({"class":"table__form"})
 * @Form\Name("previousHistory")
 */
class PreviousHistory
{
    /**
     * @Form\Name("convictions")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     * @Form\Attributes({"id":"previousConvictions"})
     */
    public $convictions = null;

    /**
     * @Form\Name("previousLicences")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     * @Form\Attributes({"id":"previousLicences"})
     */
    public $previousLicences = null;
}
