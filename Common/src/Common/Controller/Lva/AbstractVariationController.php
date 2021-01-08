<?php

/**
 * Abstract Variation Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Laminas\Form\Form;

/**
 * Abstract Variation Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractVariationController extends AbstractController
{
    use Traits\CreateVariationTrait;

    /**
     * Index action
     *
     * @return \Common\View\Model\Section
     */
    public function indexAction()
    {
        $form = $this->processForm();

        if (! ($form instanceof Form)) {
            return $form;
        }

        $translator = $this->getServiceLocator()->get('Helper\Translation');

        return $this->render(
            'create-variation-confirmation',
            $form,
            ['sectionText' => $translator->translate('markup-licence-changes-confirmation-text')]
        );
    }
}
