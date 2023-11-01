<?php

namespace Common\Form\Annotation;

use Laminas\Code\Annotation\AnnotationCollection;
use Laminas\Form\Annotation\AnnotationBuilder;
use Laminas\Form\Annotation\Flags;
use ReflectionProperty;
use ArrayObject;
use Laminas\EventManager\Event;

class CustomAnnotationBuilder
{
    private $annotationBuilder;

    public function __construct(AnnotationBuilder $annotationBuilder)
    {
        $this->annotationBuilder = $annotationBuilder;
    }

    public function configureElement(
        AnnotationCollection $annotations,
        ReflectionProperty $reflection,
        ArrayObject $formSpec,
        ArrayObject $filterSpec
    ): void {
        // Access the protected configureElement method using reflection
        $method = new \ReflectionMethod($this->annotationBuilder, 'configureElement');
        $method->setAccessible(true);
        $method->invoke($this->annotationBuilder, $annotations, $reflection, $formSpec, $filterSpec);

        // The below code is directly from your original CustomAnnotationBuilder::configureElement method

        if ($this->checkForExclude($annotations)) {
            return;
        }

        $events = $this->getEventManager();
        $name   = $this->discoverName($annotations, $reflection);

        $elementSpec = new ArrayObject(
            array(
                'flags' => array(),
                'spec'  => array(
                    'name' => $name
                ),
            )
        );
        $inputSpec = new ArrayObject(
            array(
                'name' => $name,
            )
        );

        $event = new Event();
        $event->setParams(
            array(
                'name'        => $name,
                'elementSpec' => $elementSpec,
                'inputSpec'   => $inputSpec,
                'formSpec'    => $formSpec,
                'filterSpec'  => $filterSpec,
            )
        );
        foreach ($annotations as $annotation) {
            $event->setParam('annotation', $annotation);
            $events->trigger(__FUNCTION__, $this, $event);
        }

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

    public function __call($name, $arguments)
    {
        try {
            $method = new \ReflectionMethod($this->annotationBuilder, $name);
        } catch (\ReflectionException $e) {
            // Handle non-existent method
            throw new \BadMethodCallException(
                sprintf('Method %s::%s does not exist.', get_class($this->annotationBuilder), $name)
            );
        }

        $method->setAccessible(true);

        return $method->invokeArgs($this->annotationBuilder, $arguments);
    }
}
