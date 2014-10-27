<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("lva-psv-discs-data")
 */
class PsvDiscsData
{
    /**
     * @Form\Name("validDiscs")
     * @Form\Options({
     *     "label": "application_vehicle-safety_discs-psv.validDiscs"
     * })
     * @Form\Attributes({"disabled":"disabled"})
     * @Form\Type("text")
     */
    public $validDiscs;

    /**
     * @Form\Name("pendingDiscs")
     * @Form\Options({
     *     "label": "application_vehicle-safety_discs-psv.pending"
     * })
     * @Form\Attributes({"disabled":"disabled"})
     * @Form\Type("text")
     */
    public $pendingDiscs;
}
