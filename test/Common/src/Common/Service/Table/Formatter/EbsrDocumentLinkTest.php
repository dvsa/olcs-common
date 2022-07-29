<?php

namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Formatter\EbsrDocumentLink;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @see EbsrDocumentLink
 */
class EbsrDocumentLinkTest extends MockeryTestCase
{
    /**
     * Tests format
     *
     * @param string $ebsrStatus
     * @param string $colour
     * @param string $label
     *
     * @return void
     */
    public function testFormat()
    {
        $sut = new EbsrDocumentLink();

        $submissionId = 123;
        $documentDescription = 'description';
        $url = 'http://url.com';

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get->fromRoute')
            ->once()
            ->with(EbsrDocumentLink::URL_ROUTE, ['id' => $submissionId, 'action' => EbsrDocumentLink::URL_ACTION])
            ->andReturn($url);

        $data = [
            'document' => [
                'description' => $documentDescription
            ],
            'id' => $submissionId
        ];

        $expected = sprintf(EbsrDocumentLink::LINK_PATTERN, $url, $documentDescription);

        $this->assertEquals($expected, $sut->format($data, [], $sm));
    }
}
