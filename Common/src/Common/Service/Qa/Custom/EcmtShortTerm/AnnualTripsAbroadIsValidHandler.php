<?php

namespace Common\Service\Qa\Custom\EcmtShortTerm;

use Common\Form\QaForm;
use Common\Service\Qa\IsValidHandlerInterface;

class AnnualTripsAbroadIsValidHandler implements IsValidHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid(QaForm $form)
    {
        $applicationStep = $form->getApplicationStep();
        $questionData = $form->getQuestionFieldsetData();

        $intensityWarningThreshold = $applicationStep['element']['intensityWarningThreshold'];
        $permitsRequired = intval($questionData['qaElement']);

        return ($permitsRequired <= $intensityWarningThreshold || $questionData['warningVisible'] != 0);
    }
}
