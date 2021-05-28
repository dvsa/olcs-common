<?php

declare(strict_types=1);

namespace Common\Form;

use Laminas\Form\Element\Csrf;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputInterface;

/**
 * A trait to satisfy the FormWithCsrfInterface interface.
 *
 * @see FormWithCsrfInterface
 * @see \CommonTest\Form\View\Helper\FormWithCsrfTraitTest
 */
trait FormWithCsrfTrait
{
    /**
     * Initialises a child csrf element.
     *
     * Should ideally be called by the constructor of any form.
     */
    protected function initialiseCsrf()
    {
        // Build element
        $csrfElement = new Csrf(FormWithCsrfInterface::SECURITY);
        $this->add($csrfElement);

        // Build input
        $input = new Input(FormWithCsrfInterface::SECURITY);
        $input->setRequired(true);
        $validatorChain = $input->getValidatorChain();
        $validatorChain->attach($csrfElement->getCsrfValidator());

        $this->getInputFilter()->add($input);
    }

    /**
     * @return Csrf
     */
    public function getCsrfElement(): Csrf
    {
        return $this->get(FormWithCsrfInterface::SECURITY);
    }

    /**
     * @return InputInterface
     */
    public function getCsrfInput(): InputInterface
    {
        return $this->getInputFilter()->get(FormWithCsrfInterface::SECURITY);
    }
}
