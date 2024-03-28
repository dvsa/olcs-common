<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Common\Form\QaForm;
use Common\Service\Qa\Custom\Common\WarningAdder;
use Common\Service\Qa\DataHandlerInterface;

class StandardAndCabotageDataHandler implements DataHandlerInterface
{
    /** @var StandardAndCabotageSubmittedAnswerGenerator */
    private $standardAndCabotageSubmittedAnswerGenerator;

    /** @var StandardAndCabotageIsValidHandler */
    private $standardAndCabotageIsValidHandler;

    /** @var WarningAdder */
    private $warningAdder;

    /**
     * Create service instance
     *
     *
     * @return StandardAndCabotageDataHandler
     */
    public function __construct(
        StandardAndCabotageSubmittedAnswerGenerator $standardAndCabotageSubmittedAnswerGenerator,
        StandardAndCabotageIsValidHandler $standardAndCabotageIsValidHandler,
        WarningAdder $warningAdder
    ) {
        $this->standardAndCabotageSubmittedAnswerGenerator = $standardAndCabotageSubmittedAnswerGenerator;
        $this->standardAndCabotageIsValidHandler = $standardAndCabotageIsValidHandler;
        $this->warningAdder = $warningAdder;
    }

    /**
     * {@inheritdoc}
     */
    public function setData(QaForm $form): void
    {
        if ($this->standardAndCabotageIsValidHandler->isValid($form)) {
            return;
        }

        $submittedAnswer = $this->standardAndCabotageSubmittedAnswerGenerator->generate($form);
        $questionFieldset = $form->getQuestionFieldset();
        $questionFieldset->get('warningVisible')->setValue($submittedAnswer);

        $this->warningAdder->add($questionFieldset, 'qanda.bilaterals.standard-and-cabotage.warning');
    }
}
