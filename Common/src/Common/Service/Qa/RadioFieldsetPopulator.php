<?php

namespace Common\Service\Qa;

use Laminas\Form\Fieldset;

class RadioFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var RadioFactory */
    private $radioFactory;

    /** @var TranslateableTextHandler */
    private $translateableTextHandler;

    /**
     * Create service instance
     *
     * @param RadioFactory $radioFactory
     * @param TranslateableTextHandler $translateableTextHandler
     *
     * @return RadioFieldsetPopulator
     */
    public function __construct(
        RadioFactory $radioFactory,
        TranslateableTextHandler $translateableTextHandler
    ) {
        $this->radioFactory = $radioFactory;
        $this->translateableTextHandler = $translateableTextHandler;
    }

    /**
     * Populate the fieldset with a radio element based on the supplied options array
     *
     * @param mixed $form
     * @param Fieldset $fieldset
     * @param array $options
     */
    public function populate($form, Fieldset $fieldset, array $options)
    {
        $radio = $this->radioFactory->create('qaElement');

        $notSelectedMessage = $this->translateableTextHandler->translate($options['notSelectedMessage']);

        $options['options'][0]['attributes'] = [
            'id' => 'qaElement'
        ];

        $radio->setValueOptions($options['options']);
        $radio->setValue($options['value']);
        $radio->setOption('not_selected_message', $notSelectedMessage);

        $fieldset->add($radio);
    }
}
