<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Common\Service\Qa\FieldsetPopulatorInterface;
use Laminas\Form\Fieldset;

class NoOfPermitsMoroccoFieldsetPopulator implements FieldsetPopulatorInterface
{
    /**
     * {@inheritdoc}
     *
     * @param \Mockery\LegacyMockInterface&\Mockery\MockInterface&\Laminas\Form\Form $form
     */
    public function populate(\Laminas\Form\Form $form, Fieldset $fieldset, array $options): void
    {
        $fieldset->add(
            [
                'type' => NoOfPermitsElement::class,
                'name' => 'qaElement',
                'options' => [
                    'label' => $options['label'],
                ],
                'attributes' => [
                    'value' => $options['value']
                ]
            ]
        );
    }
}
