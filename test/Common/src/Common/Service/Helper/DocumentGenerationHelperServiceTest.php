<?php

/**
 * Document Generation Helper Service test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Helper;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Helper\DocumentGenerationHelperService;
use Mockery as m;

/**
 * Document Generation Helper Service test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DocumentGenerationHelperServiceTest extends MockeryTestCase
{
    public function testGenerateFromTemplateWithEmptyQuery()
    {
        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('ContentStore')
            ->andReturn(
                m::mock()
                ->shouldReceive('read')
                ->with('/templates/x.rtf')
                ->getMock()
            )
            ->shouldReceive('get')
            ->with('Document')
            ->andReturn(
                m::mock()
                ->shouldReceive('getBookmarkQueries')
                ->shouldReceive('populateBookmarks')
                ->getMock()
            )
            ->getMock();

        $helper = new DocumentGenerationHelperService();
        $helper->setServiceLocator($sm);
    }
}
