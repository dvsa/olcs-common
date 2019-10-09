<?php

namespace Common\Service\Qa\Custom\EcmtShortTerm;

use Common\Form\Elements\Types\Html;
use Common\Form\QaForm;
use Common\Service\Qa\DataHandlerInterface;
use Zend\View\Helper\Partial;

class AnnualTripsAbroadDataHandler implements DataHandlerInterface
{
    /** @var Partial */
    private $partial;

    /** @var AnnualTripsAbroadIsValidHandler */
    private $annualTripsAbroadIsValidHandler;

    /**
     * Create service instance
     *
     * @param Partial $partial
     * @param AnnualTripsAbroadIsValidHandler $annualTripsAbroadIsValidHandler
     *
     * @return AnnualTripsAbroadDataHandler
     */
    public function __construct(
        Partial $partial,
        AnnualTripsAbroadIsValidHandler $annualTripsAbroadIsValidHandler
    ) {
        $this->partial = $partial;
        $this->annualTripsAbroadIsValidHandler = $annualTripsAbroadIsValidHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function setData(QaForm $form)
    {
        if ($this->annualTripsAbroadIsValidHandler->isValid($form)) {
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
                'priority' => 10
            ]
        );
    }
}
