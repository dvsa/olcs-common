<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Common\Service\Qa\FieldsetPopulatorInterface;
use Zend\Form\Fieldset;

class NoOfPermitsFieldsetPopulator implements FieldsetPopulatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function populate($form, Fieldset $fieldset, array $options)
    {
        foreach ($options['texts'] as $text) {
            $fieldset->add(
                [
                    'type' => NoOfPermitsElement::class,
                    'name' => $text['name'],
                    'options' => [
                        'label' => $text['label'],
                        'hint' => $text['hint'],
                    ],
                    'attributes' => [
                        'value' => $text['value']
                    ]
                ]
            );
        }
    }
}
