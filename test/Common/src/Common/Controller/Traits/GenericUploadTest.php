<?php

/**
 * Generic Upload Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Traits;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use CommonTest\Controller\Traits\Stubs\GenericUploadStub;

/**
 * Generic Upload Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GenericUploadTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new GenericUploadStub();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testDeleteFile()
    {
        // Mocks
        $mockDocumentEntity = m::mock();
        $this->sm->setService('Entity\Document', $mockDocumentEntity);
        $mockFileUploader = m::mock();
        $this->sm->setService('FileUploader', $mockFileUploader);

        // Expectations
        $mockDocumentEntity->shouldReceive('getIdentifier')
            ->with(123)
            ->andReturn(987654321)
            ->shouldReceive('delete')
            ->with(123);

        $mockFileUploader->shouldReceive('getUploader->remove')
            ->with(987654321);

        $this->assertTrue($this->sut->callDeleteFile(123));
    }

    public function testDeleteFileWithoutIdentifier()
    {
        // Mocks
        $mockDocumentEntity = m::mock();
        $this->sm->setService('Entity\Document', $mockDocumentEntity);

        // Expectations
        $mockDocumentEntity->shouldReceive('getIdentifier')
            ->with(123)
            ->andReturn(null)
            ->shouldReceive('delete')
            ->with(123);

        $this->assertTrue($this->sut->callDeleteFile(123));
    }
}
