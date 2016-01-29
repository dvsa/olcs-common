<?php

/**
 * Generic Upload Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Traits;

use Dvsa\Olcs\Transfer\Command\Document\DeleteDocument;
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
        $this->sut->stubResponse = m::mock()->makePartial();
        $this->sut->stubResponse->shouldReceive('isOk')
            ->andReturn(true);

        $this->assertTrue($this->sut->callDeleteFile(123));

        $this->assertInstanceOf(DeleteDocument::class, $this->sut->stubResponse->dto);
        $this->assertEquals(123, $this->sut->stubResponse->dto->getId());
    }
}
