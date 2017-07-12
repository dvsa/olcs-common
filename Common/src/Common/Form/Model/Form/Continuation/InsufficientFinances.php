<?php

namespace Common\Form\Model\Form\Continuation;

use Zend\Form\Annotation as Form;

/**
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\InsufficientFinancesForm")
 */
class InsufficientFinances
{
    /**
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\ComposedObject("Common\Form\Model\Form\Continuation\Fieldset\InsufficientFinancesSummary")
     */
    public $insufficientFinancesSummary = null;

    /**
     * @Form\ComposedObject("Common\Form\Model\Form\Continuation\Fieldset\InsufficientFinances")
     */
    public $insufficientFinances = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--primary large"})
     * @Form\Options({"label":"Continue"})
     * @Form\Type("\Zend\Form\Element\Button")
     */
    public $submit = null;
}
