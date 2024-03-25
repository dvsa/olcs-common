<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Common\Form\QaForm;
use Common\Service\Qa\Custom\Common\IsValidBasedWarningAdder;
use Common\Service\Qa\DataHandlerInterface;

class PermitUsageDataHandler implements DataHandlerInterface
{
    /** @var IsValidBasedWarningAdder */
    private $isValidBasedWarningAdder;

    /** @var PermitUsageIsValidHandler */
    private $permitUsageIsValidHandler;

    /**
     * Create service instance
     *
     *
     * @return PermitUsageDataHandler
     */
    public function __construct(
        IsValidBasedWarningAdder $isValidBasedWarningAdder,
        PermitUsageIsValidHandler $permitUsageIsValidHandler
    ) {
        $this->isValidBasedWarningAdder = $isValidBasedWarningAdder;
        $this->permitUsageIsValidHandler = $permitUsageIsValidHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function setData(QaForm $form): void
    {
        $this->isValidBasedWarningAdder->add(
            $this->permitUsageIsValidHandler,
            $form,
            'qanda.bilaterals.permit-usage.warning'
        );
    }
}
