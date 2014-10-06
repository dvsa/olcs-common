<?php
/**
 * OLCS custom form annotation builder
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */

namespace Common\Form\Annotation;

use Zend\Form\Annotation\AnnotationBuilder;

/**
 * OLCS custom form annotation builder
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class CustomAnnotationBuilder extends AnnotationBuilder
{
    /**
     * Overrides default zend annotation function
     * Fixes a problem we're having with fieldsets rendering not where we want them
     *
     * @param string $className
     * @param string $type
     * @return bool
     */
    protected static function isSubclassOf($className, $type)
    {
        return false;
    }
}
