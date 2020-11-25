<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Common\Form\QaForm;
use Common\Service\Qa\IsValidHandlerInterface;

class StandardAndCabotageIsValidHandler implements IsValidHandlerInterface
{
    /** @var StandardAndCabotageSubmittedAnswerGenerator */
    private $standardAndCabotageSubmittedAnswerGenerator;

    /**
     * Create service instance
     *
     * @param StandardAndCabotageSubmittedAnswerGenerator $standardAndCabotageSubmittedAnswerGenerator
     *
     * @return StandardAndCabotageIsValidHandler
     */
    public function __construct(
        StandardAndCabotageSubmittedAnswerGenerator $standardAndCabotageSubmittedAnswerGenerator
    ) {
        $this->standardAndCabotageSubmittedAnswerGenerator = $standardAndCabotageSubmittedAnswerGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(QaForm $form)
    {
        $applicationStep = $form->getApplicationStep();
        $storedAnswer = $applicationStep['element']['value'];

        $questionData = $form->getQuestionFieldsetData();
        $submittedAnswer = $this->standardAndCabotageSubmittedAnswerGenerator->generate($form);

        return (
            is_null($storedAnswer) ||
            $submittedAnswer == '' ||
            ($storedAnswer == $submittedAnswer) ||
            ($storedAnswer != $submittedAnswer) && ($questionData['warningVisible'] == $submittedAnswer)
        );
    }
}
