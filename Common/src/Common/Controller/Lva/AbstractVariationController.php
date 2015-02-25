<?php

/**
 * Abstract Variation Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Zend\Form\Form;

/**
 * Abstract Variation Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractVariationController extends AbstractController
{
    use Traits\CreateVariationTrait;

    public function indexAction()
    {
        $form = $this->processForm();

        if (! ($form instanceof Form)) {
            return $form;
        }

        return $this->render(
            'create-variation-confirmation',
            $form,
            ['sectionText' => 'licence.variation.confirmation.text']
        );
    }
}
