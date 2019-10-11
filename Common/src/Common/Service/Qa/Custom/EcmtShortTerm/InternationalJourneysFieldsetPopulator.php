<?php

namespace Common\Service\Qa\Custom\EcmtShortTerm;

use Common\Form\Elements\Types\Html;
use Common\Service\Qa\FieldsetPopulatorInterface;
use Common\Service\Qa\RadioFieldsetPopulator;
use Zend\Form\Element\Hidden;
use Zend\Form\Fieldset;
use Zend\View\Helper\Partial;

class InternationalJourneysFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var RadioFieldsetPopulator */
    private $radioFieldsetPopulator;

    /** @var Partial */
    private $partial;

    /**
     * Create service instance
     *
     * @param RadioFieldsetPopulator $radioFieldsetPopulator
     * @param Partial $partial
     *
     * @return InternationalJourneysFieldsetPopulator
     */
    public function __construct(RadioFieldsetPopulator $radioFieldsetPopulator, Partial $partial)
    {
        $this->radioFieldsetPopulator = $radioFieldsetPopulator;
        $this->partial = $partial;
    }

    /**
     * Populate the fieldset with elements based on the supplied options array
     *
     * @param mixed $form
     * @param Fieldset $fieldset
     * @param array $options
     */
    public function populate($form, Fieldset $fieldset, array $options)
    {
        if ($options['showNiWarning']) {
            $markup = $this->partial->__invoke(
                'partials/warning-component',
                ['translationKey' => 'permits.page.number-of-trips.northern-ireland.warning']
            );

            $fieldset->add(
                [
                    'name' => 'niWarning',
                    'type' => Html::class,
                    'attributes' => [
                        'value' => $markup
                    ]
                ]
            );
        }

        $fieldset->add(
            [
                'name' => 'warningVisible',
                'type' => Hidden::class,
                'attributes' => [
                    'value' => 0
                ]
            ]
        );

        $this->radioFieldsetPopulator->populate($form, $fieldset, $options['radio']);
    }
}
