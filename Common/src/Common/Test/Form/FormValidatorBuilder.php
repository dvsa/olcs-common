<?php

declare(strict_types=1);

namespace Common\Test\Form;

use Common\Form\FormValidator;
use Laminas\Form\Form;
use Laminas\Form\Element\Csrf;

/**
 * @see FormValidator
 * @see \CommonTest\Test\Form\FormValidationBuilderTest
 */
class FormValidatorBuilder
{
    /**
     * @var bool
     */
    protected $populateCsrfDataBeforeValidating = false;

    /**
     * @return static
     */
    public static function aValidator(): self
    {
        return new static();
    }

    /**
     * When disabled, a form validator will disable any validation on the csrf elements of any forms that it is asked to
     * validate.
     *
     * @return self
     */
    public function populateCsrfDataBeforeValidating(): self
    {
        $this->populateCsrfDataBeforeValidating = true;
        return $this;
    }

    /**
     * @return FormValidator
     */
    public function build(): FormValidator
    {
        $instance = new class extends FormValidator {
            /**
             * @var bool
             */
            private $populateCsrfDataBeforeValidating = false;

            /**
             * @param bool $disabled
             */
            public function setPopulateCsrfDataBeforeValidating(bool $disabled = true)
            {
                $this->populateCsrfDataBeforeValidating = $disabled;
            }

            /**
             * @param Form $form
             * @return bool
             */
            public function isValid(Form $form): bool
            {
                if ($this->populateCsrfDataBeforeValidating) {
                    $csrfData = [];
                    $elements = $form->getElements();
                    while ($element = array_pop($elements)) {
                        if ($element instanceof Csrf) {
                            $csrfData[$element->getName()] = $element->getCsrfValidator()->getHash();
                        }
                    }
                    if (! empty($csrfData)) {
                        $form->setData(array_merge($form->getInputFilter()->getRawValues(), $csrfData));
                    }
                }
                return parent::isValid($form);
            }
        };

        if (true === $this->populateCsrfDataBeforeValidating) {
            $instance->setPopulateCsrfDataBeforeValidating();
        }

        return $instance;
    }
}
