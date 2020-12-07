<?php

namespace Common\Controller\Lva;

use Common\Controller\Lva\Traits\CrudActionTrait;
use Laminas\Form\Form;

/**
 * Common Lva Abstract Type Of Licence Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractTypeOfLicenceController extends AbstractController
{
    use CrudActionTrait;

    /** @var string */
    protected $baseRoute = 'lva-%s/type_of_licence';

    /**
     * Render for Index action
     *
     * @param \Laminas\Form\FormInterface $form Form
     *
     * @return \Common\View\Model\Section
     */
    protected function renderIndex($form)
    {
        $this->getServiceLocator()->get('Script')->loadFile('type-of-licence');

        return $this->render('type_of_licence', $form);
    }

    /**
     * Process error messages from API
     *
     * @param \Laminas\Form\FormInterface $form   Form
     * @param array                    $errors Errors
     *
     * @return void
     */
    protected function mapErrors(Form $form, array $errors)
    {
        $formMessages = [];

        if (isset($errors['licenceType'])) {
            foreach ($errors['licenceType'][0] as $key => $message) {
                $formMessages['type-of-licence']['licence-type'][] = $key;
            }

            unset($errors['licenceType']);
        }

        if (isset($errors['goodsOrPsv'])) {
            foreach ($errors['goodsOrPsv'][0] as $key => $message) {
                $formMessages['type-of-licence']['operator-type'][] = $key;
            }

            unset($errors['licenceType']);
        }

        if (!empty($errors)) {
            $fm = $this->getServiceLocator()->get('Helper\FlashMessenger');

            foreach ($errors as $error) {
                $fm->addCurrentErrorMessage($error);
            }
        }

        $form->setMessages($formMessages);
    }
}
