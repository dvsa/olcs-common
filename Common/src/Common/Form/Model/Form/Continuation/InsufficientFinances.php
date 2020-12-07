<?php

namespace Common\Form\Model\Form\Continuation;

use Laminas\Form\Annotation as Form;

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
}
