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
     * @return \Zend\Form\Form|mixed
     * @throws \Exception
     */
    public function createForm($formName)
    {
        $class = $this->findForm($formName);

        $annotationBuilder = $this->getServiceLocator()->get('FormAnnotationBuilder');

        return $annotationBuilder->createForm($class);
    }

    /**
     * Find form
     *
     * @param string $formName
     * @return string
     * @throws \Exception
     */
    private function findForm($formName)
    {
        foreach (['Olcs', 'Common'] as $namespace) {
            $class = $namespace . '\Form\Model\Form\\' . $formName;

            if (class_exists($class)) {
                return $class;
            }
        }

        throw new \Exception('Form does not exist: ' . $formName);
    }
}
