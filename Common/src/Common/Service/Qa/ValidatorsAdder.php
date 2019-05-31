<?php

namespace Common\Service\Qa;

class ValidatorsAdder
{
    /**
     * Add validators for a single fieldset to the specified form using the supplied array representation
     *
     * @param mixed $form
     * @param string $fieldsetName
     * @param array $validators
     */
    public function add($form, $fieldsetName, array $validators)
    {
        $input = $form->getInputFilter()->get($fieldsetName)->get('qaElement');
        $input->setContinueIfEmpty(true);
        $validatorChain = $input->getValidatorChain();

        foreach ($validators as $validator) {
            $validatorChain->attachByName(
                $validator['rule'],
                $validator['params']
            );
        }
    }
}
