<?php

declare(strict_types=1);

namespace Common\Form;

use Laminas\Form\Element\Csrf;
use Laminas\InputFilter\InputInterface;

/**
 * @see FormWithCsrfTrait
 */
interface FormWithCsrfInterface
{
    public const SECURITY = 'security';

    /**
     * @return Csrf
     */
    public function getCsrfElement(): Csrf;

    /**
     * @return InputInterface
     */
    public function getCsrfInput(): InputInterface;
}
