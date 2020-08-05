<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Common\Form\Elements\Types\Html;
use Common\Form\QaForm;
use Common\Service\Qa\Custom\Common\IsValidBasedWarningAdder;
use Common\Service\Qa\DataHandlerInterface;

class InternationalJourneysDataHandler implements DataHandlerInterface
{
    /** @var IsValidBasedWarningAdder */
    private $isValidBasedWarningAdder;

    /** @var InternationalJourneysIsValidHandler */
    private $internationalJourneysIsValidHandler;

    /**
     * Create service instance
     *
     * @param IsValidBasedWarningAdder $isValidBasedWarningAdder
     * @param InternationalJourneysIsValidHandler $internationalJourneysIsValidHandler
     *
     * @return InternationalJourneysDataHandler
     */
    public function __construct(
        IsValidBasedWarningAdder $isValidBasedWarningAdder,
        InternationalJourneysIsValidHandler $internationalJourneysIsValidHandler
    ) {
        $this->isValidBasedWarningAdder = $isValidBasedWarningAdder;
        $this->internationalJourneysIsValidHandler = $internationalJourneysIsValidHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function setData(QaForm $form)
    {
        $this->isValidBasedWarningAdder->add(
            $this->internationalJourneysIsValidHandler,
            $form,
            'permits.form.trips.warning',
            20
        );
    }
}
