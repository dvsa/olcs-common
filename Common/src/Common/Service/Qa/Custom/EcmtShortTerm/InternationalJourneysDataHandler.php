<?php

namespace Common\Service\Qa\Custom\EcmtShortTerm;

use Common\Form\Elements\Types\Html;
use Common\Form\QaForm;
use Common\Service\Qa\DataHandlerInterface;
use Zend\View\Helper\Partial;

class InternationalJourneysDataHandler implements DataHandlerInterface
{
    /** @var Partial */
    private $partial;

    /** @var InternationalJourneysIsValidHandler */
    private $internationalJourneysIsValidHandler;

    /**
     * Create service instance
     *
     * @param Partial $partial
     * @param InternationalJourneysIsValidHandler $internationalJourneysIsValidHandler
     *
     * @return InternationalJourneysDataHandler
     */
    public function __construct(
        Partial $partial,
        InternationalJourneysIsValidHandler $internationalJourneysIsValidHandler
    ) {
        $this->partial = $partial;
        $this->internationalJourneysIsValidHandler = $internationalJourneysIsValidHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function setData(QaForm $form)
    {
        if ($this->internationalJourneysIsValidHandler->isValid($form)) {
            return;
        }

        $questionFieldset = $form->getQuestionFieldset();
        $questionFieldset->get('warningVisible')->setValue(1);

        $markup = $this->partial->__invoke(
            'partials/warning-component',
            ['translationKey' => 'permits.form.trips.warning']
        );

        $questionFieldset->add(
            [
                'name' => 'intensityWarning',
                'type' => Html::class,
                'attributes' => [
                    'value' => $markup
                ]
            ],
            [
                'priority' => 20
            ]
        );
    }
}
