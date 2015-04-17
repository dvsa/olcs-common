<?php

/**
 * Inspection Request Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonComponentTest\Lva;

use PHPUnit_Framework_TestCase;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;

/**
 * Inspection Request Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class InspectionRequestTest extends PHPUnit_Framework_TestCase
{
    public function testView()
    {
        $view = new ViewModel();
        $view->setTemplate('email/inspection-request.phtml');

        $renderer = new PhpRenderer();

        $renderer->render($view);
    }
}
