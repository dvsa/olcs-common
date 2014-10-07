<?php

/**
 * Form Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

/**
 * Form Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormHelperService extends AbstractHelperService
{
    public function createForm($formName)
    {
        $class = 'Common\Form\Model\Form\\' . $formName;

        if (!class_exists($class)) {
            throw new \Exception('Form does not exist: ' . $formName);
        }

        $annotationBuilder = $this->getServiceLocator()->get('FormAnnotationBuilder');
        $annotationBuilder->createForm($class);
    }
}
