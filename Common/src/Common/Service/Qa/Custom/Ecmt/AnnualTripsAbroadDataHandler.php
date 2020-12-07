<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Common\Form\QaForm;
use Common\Service\Qa\Custom\Common\IsValidBasedWarningAdder;
use Common\Service\Qa\DataHandlerInterface;
use Laminas\View\Helper\Partial;

class AnnualTripsAbroadDataHandler implements DataHandlerInterface
{
    /** @var IsValidBasedWarningAdder */
    private $isValidBasedWarningAdder;

    /** @var AnnualTripsAbroadIsValidHandler */
    private $annualTripsAbroadIsValidHandler;

    /**
     * Create service instance
     *
     * @param IsValidBasedWarningAdder $isValidBasedWarningAdder
     * @param AnnualTripsAbroadIsValidHandler $annualTripsAbroadIsValidHandler
     *
     * @return AnnualTripsAbroadDataHandler
     */
    public function __construct(
        IsValidBasedWarningAdder $isValidBasedWarningAdder,
        AnnualTripsAbroadIsValidHandler $annualTripsAbroadIsValidHandler
    ) {
        $this->isValidBasedWarningAdder = $isValidBasedWarningAdder;
        $this->annualTripsAbroadIsValidHandler = $annualTripsAbroadIsValidHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function setData(QaForm $form)
    {
        $this->isValidBasedWarningAdder->add(
            $this->annualTripsAbroadIsValidHandler,
            $form,
            'permits.form.trips.warning'
        );
    }
}
