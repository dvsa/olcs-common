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
    /**
     * Create a form
     *
     * @param string $formName
     * @return \Zend\Form\Form
     * @throws \Exception
     */
    public function createForm($formName)
    {
        $class = 'Common\Form\Model\Form\\' . $formName;

        if (!class_exists($class)) {
            throw new \Exception('Form does not exist: ' . $class);
        }

        $annotationBuilder = $this->getServiceLocator()->get('FormAnnotationBuilder');
        return $annotationBuilder->createForm($class);
    }
}
