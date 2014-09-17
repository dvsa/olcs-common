<?php

/**
 * Test Back Button Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Traits;

/**
 * Test Back Button Trait
 *
 * @NOTE to prevent duplicated code
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait TestBackButtonTrait
{
    /**
     * Test back button
     */
    public function testBackButton()
    {
        $this->setUpAction('index', null, array('form-actions' => array('back' => 'Back')));

        $response = $this->controller->indexAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }
}
