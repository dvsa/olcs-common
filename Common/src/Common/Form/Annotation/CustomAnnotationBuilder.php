<?php
/**
 * OLCS custom form annotation builder
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */

namespace Common\Form\Annotation;

use Zend\Form\Annotation\AnnotationBuilder;
use ArrayObject;
use Zend\Code\Annotation\AnnotationCollection;
use Zend\Code\Annotation\Parser;
use Zend\EventManager\Event;
use Zend\Form\Exception;

/**
 * OLCS custom form annotation builder
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class CustomAnnotationBuilder extends AnnotationBuilder
{
    /**
     * Configure an element from annotations
     *
     * @param  AnnotationCollection $annotations
     * @param  \Zend\Code\Reflection\PropertyReflection $reflection
     * @param  ArrayObject $formSpec
     * @param  ArrayObject $filterSpec
     * @return void
     * @triggers checkForExclude
     * @triggers discoverName
     * @triggers configureElement
     */
    protected function configureElement($annotations, $reflection, $formSpec, $filterSpec)
    {
        // If the element is marked as exclude, return early
        if ($this->checkForExclude($annotations)) {
            return;
        }

        $events = $this->getEventManager();
        $name   = $this->discoverName($annotations, $reflection);

        $elementSpec = new ArrayObject(array(
            'flags' => array(),
            'spec'  => array(
                'name' => $name
            ),
        ));
        $inputSpec = new ArrayObject(array(
            'name' => $name,
        ));

        $event = new Event();
        $event->setParams(array(
            'name'        => $name,
            'elementSpec' => $elementSpec,
            'inputSpec'   => $inputSpec,
            'formSpec'    => $formSpec,
            'filterSpec'  => $filterSpec,
        ));
        foreach ($annotations as $annotation) {
            $event->setParam('annotation', $annotation);
            $events->trigger(__FUNCTION__, $this, $event);
        }

        // Since "type" is a reserved name in the filter specification,
        // we need to add the specification without the name as the key.
        // In all other cases, though, the name is fine.
        if ($event->getParam('inputSpec')->count() > 1) {
            if ($name === 'type') {
                $filterSpec[] = $event->getParam('inputSpec');
            } else {
                $filterSpec[$name] = $event->getParam('inputSpec');
            }
        }

        if (!isset($formSpec['elements'])) {
            $formSpec['elements'] = array();
        }
        $formSpec['elements'][] = $event->getParam('elementSpec');
    }
}
