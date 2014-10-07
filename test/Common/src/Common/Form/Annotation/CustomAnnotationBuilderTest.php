<?php

namespace CommonTest\Form\Annotation;

use Common\Form\Annotation\CustomAnnotationBuilder;

/**
 * Class CustomAnnotationBuilderTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class CustomAnnotationBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * We override this Zend method so it always returns false
     */
    public function testIsSubclassOfReturnsFalse()
    {
        $sut = new CustomAnnotationBuilder();
        $this->assertEquals(false, $sut::isSubclassOf('',''));
    }
}