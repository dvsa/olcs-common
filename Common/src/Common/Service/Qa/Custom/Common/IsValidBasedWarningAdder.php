<?php

namespace Common\Service\Qa\Custom\Common;

use Common\Form\QaForm;
use Common\Service\Qa\IsValidHandlerInterface;

class IsValidBasedWarningAdder
{
    /** @var WarningAdder */
    private $warningAdder;

    /**
     * Create service instance
     *
     * @param WarningAdder $warningAdder
     *
     * @return IsValidBasedWarningAdder
     */
    public function __construct(WarningAdder $warningAdder)
    {
        $this->warningAdder = $warningAdder;
    }

    /**
     * Add a warning partial to the form if the is valid handler returns false
     *
     * @param IsValidHandlerInterface $isValidHandler
     * @param QaForm $form
     * @param string $warningKey
     * @param int $priority
     */
    public function add(
        IsValidHandlerInterface $isValidHandler,
        QaForm $form,
        $warningKey,
        $priority = WarningAdder::DEFAULT_PRIORITY
    ) {
        if ($isValidHandler->isValid($form)) {
            return;
        }

        $questionFieldset = $form->getQuestionFieldset();
        $questionFieldset->get('warningVisible')->setValue(1);

        $this->warningAdder->add($questionFieldset, $warningKey, $priority);
    }
}
