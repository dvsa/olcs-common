<?php
/**
 * Test FlashMessengerTrait
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 */

namespace CommonTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class FlashMessengerTraitTest extends AbstractHttpControllerTestCase
{

    public function setUp()
    {
        //$this->setApplicationConfig(
        //    include __DIR__.'/../../../../../'  . 'config/application.config.php'
        //);
        
        $submissionSectionTrait = $this->getMockForTrait(
            '\Common\Util\FlashMessengerTrait',
            array(),
            '',
            true,
            true,
            true,
            array(
                'makeRestCall',
                'getParams',
                'getFilteredSectionData',
                'log'
            )
        );
    }

    public function testGetFlashMessenger()
    {
        $returned = $this->resolveApi->getFlashMessenger('backend\VosaCase');
        //$this->assertTrue(get_class($returned) === 'Common\Util\RestClient');
    }
}
