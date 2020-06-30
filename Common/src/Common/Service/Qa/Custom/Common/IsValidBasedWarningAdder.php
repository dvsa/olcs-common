<?php

namespace Common\Service\Qa\Custom\Common;

use Common\Form\Elements\Types\Html;
use Common\Form\QaForm;
use Common\Service\Qa\IsValidHandlerInterface;
use Zend\View\Helper\Partial;

class IsValidBasedWarningAdder
{
    const DEFAULT_PRIORITY = 10;

    /** @var Partial */
    private $partial;

    /**
     * Create service instance
     *
     * @param Partial $partial
     *
     * @return IsValidBasedWarningAdder
     */
    public function __construct(Partial $partial)
    {
        $this->partial = $partial;
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
        $priority = self::DEFAULT_PRIORITY
    ) {
        if ($isValidHandler->isValid($form)) {
            return;
        }

        $questionFieldset = $form->getQuestionFieldset();
        $questionFieldset->get('warningVisible')->setValue(1);

        $markup = $this->partial->__invoke(
            'partials/warning-component',
            ['translationKey' => $warningKey]
        );

        $questionFieldset->add(
            [
                'name' => 'warning',
                'type' => Html::class,
                'attributes' => [
                    'value' => $markup
                ]
            ],
            [
                'priority' => $priority
            ]
        );
    }
}
