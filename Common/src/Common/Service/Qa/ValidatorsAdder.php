<?php

namespace Common\Service\Qa;

class ValidatorsAdder
{
    /**
     * Add validators for a single fieldset to the specified form using the supplied array representation
     *
     * @param mixed $form
     * @param array $options
     */
    public function add($form, array $options)
    {
        $validators = $options['validators'];

        if (count($validators) > 0) {
            $fieldsetName = $options['fieldsetName'];

            $input = $form->getInputFilter()->get('qa')->get($fieldsetName)->get('qaElement');
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
}
